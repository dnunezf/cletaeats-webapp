<?php

/**
 * Self-service profile updates for the currently authenticated user.
 *
 * Role/status/document are NOT editable here — those remain admin-managed
 * through the existing user CRUD. This service only touches what the user is
 * allowed to change about themselves: identity (username, email), location
 * (address, city, postal_code), password, optional photo, and the
 * customer-specific card_number when role=customer.
 */
class ProfileService
{
    private UserRepository $userRepo;
    private LocationRepository $locationRepo;
    private CustomerRepository $customerRepo;

    private const MAX_PHOTO_BYTES = 2 * 1024 * 1024; // 2 MB
    private const ALLOWED_MIMES   = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    public function __construct()
    {
        $this->userRepo     = new UserRepository();
        $this->locationRepo = new LocationRepository();
        $this->customerRepo = new CustomerRepository();
    }

    /**
     * Returns a flat array combining user + location (+ customer card_number when applicable).
     * Used by the profile view to seed form fields.
     */
    public function getProfile(int $userId): ?array
    {
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            return null;
        }
        $location = $this->locationRepo->findById((int) $user['location_id']);
        $profile = array_merge($user, [
            'address'     => $location['address']     ?? '',
            'city'        => $location['city']        ?? '',
            'postal_code' => $location['postal_code'] ?? '',
        ]);

        if (($user['role'] ?? '') === 'customer') {
            $cust = $this->customerRepo->findById($userId);
            $profile['card_number'] = $cust['card_number'] ?? '';
        }
        return $profile;
    }

    /**
     * Validates + persists the user's own profile changes. Returns true on success
     * or an associative array of field => error message on failure.
     *
     * Photo uploads are processed last so an upload failure cannot leave the rest
     * of the profile half-saved.
     */
    public function update(int $userId, array $data, array $files): bool|array
    {
        $existing = $this->userRepo->findById($userId);
        if (!$existing) {
            return ['general' => 'Profile not found.'];
        }

        $v = new Validator();
        $v->required($data['username'] ?? '', 'username')
          ->alphanumeric($data['username'] ?? '', 'username')
          ->minLength($data['username'] ?? '', 3, 'username')
          ->maxLength($data['username'] ?? '', 255, 'username')
          ->required($data['email'] ?? '', 'email')
          ->email($data['email'] ?? '', 'email')
          ->maxLength($data['email'] ?? '', 255, 'email')
          ->required($data['address'] ?? '', 'address')
          ->maxLength($data['address'] ?? '', 255, 'address')
          ->required($data['city'] ?? '', 'city')
          ->maxLength($data['city'] ?? '', 255, 'city')
          ->required($data['postal_code'] ?? '', 'postal_code')
          ->maxLength($data['postal_code'] ?? '', 255, 'postal_code');

        if (!empty($data['password'])) {
            $v->password($data['password'], 'password')
              ->matches($data['password'], $data['password_confirm'] ?? '', 'password');
        }

        if (($existing['role'] ?? '') === 'customer') {
            $card = trim((string) ($data['card_number'] ?? ''));
            if ($card !== '' && !preg_match('/^\d{13,19}$/', $card)) {
                $v->required('', 'card_number'); // marker
            }
        }

        if (!$v->isValid()) {
            return $v->getFirstErrors();
        }

        // Uniqueness checks scoped to "not this user".
        if ($this->userRepo->findByUsernameExcluding($data['username'], $userId)) {
            return ['username' => 'This username is already taken.'];
        }
        if ($this->userRepo->findByEmailExcluding($data['email'], $userId)) {
            return ['email' => 'This email is already registered.'];
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $locationId = (int) $existing['location_id'];
            $this->locationRepo->update($locationId, [
                'address'     => trim($data['address']),
                'city'        => trim($data['city']),
                'postal_code' => trim($data['postal_code']),
            ]);

            // role/status/document are preserved verbatim from the existing record.
            $this->userRepo->update($userId, [
                'username'      => trim($data['username']),
                'email'         => trim($data['email']),
                'password_hash' => $existing['password_hash'],
                'role'          => $existing['role'],
                'status'        => $existing['status'],
                'document'      => $existing['document'],
                'location_id'   => $locationId,
            ]);

            if (!empty($data['password'])) {
                $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
                $this->userRepo->updatePassword($userId, $hash);
            }

            if (($existing['role'] ?? '') === 'customer' && !empty($data['card_number'])) {
                $this->customerRepo->update($userId, trim((string) $data['card_number']));
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['general' => 'Unable to save profile: ' . $e->getMessage()];
        }

        // Photo upload runs *after* the DB write; failures here only surface as a
        // field-level error without rolling back the rest of the save.
        if (!empty($files['photo']['name'] ?? '')) {
            $photoErr = $this->storeAvatar($userId, $files['photo']);
            if ($photoErr !== null) {
                return ['photo' => $photoErr];
            }
        }

        // Optional "remove photo" toggle.
        if (!empty($data['remove_photo'])) {
            $this->removeAvatar($userId);
        }

        // Keep the in-session username in sync so the nav reflects the change immediately.
        if (isset($_SESSION['username'])) {
            $_SESSION['username'] = trim($data['username']);
        }

        return true;
    }

    /** Persists the uploaded image as user_{id}.{ext}. Returns null on success, or an error string. */
    private function storeAvatar(int $userId, array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return 'Upload failed. Please try again.';
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return 'Upload failed. Please try again.';
        }
        if (($file['size'] ?? 0) > self::MAX_PHOTO_BYTES) {
            return 'Photo must be 2 MB or smaller.';
        }

        // Determine MIME from the file contents — do not trust the client.
        $detected = function_exists('mime_content_type')
            ? @mime_content_type($file['tmp_name'])
            : null;
        if (!$detected || !isset(self::ALLOWED_MIMES[$detected])) {
            return 'Only JPEG, PNG, WEBP, or GIF images are allowed.';
        }
        $ext = self::ALLOWED_MIMES[$detected];

        $dir = BASE_PATH . AVATAR_DIR;
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            return 'Server is not configured to store images.';
        }

        // Wipe any previous extension before writing the new one.
        $this->removeAvatar($userId);

        $target = $dir . '/user_' . $userId . '.' . $ext;
        if (!@move_uploaded_file($file['tmp_name'], $target)) {
            return 'Could not save the uploaded image.';
        }
        @chmod($target, 0644);
        return null;
    }

    private function removeAvatar(int $userId): void
    {
        foreach (AVATAR_EXTENSIONS as $ext) {
            $candidate = BASE_PATH . AVATAR_DIR . '/user_' . $userId . '.' . $ext;
            if (is_file($candidate)) {
                @unlink($candidate);
            }
        }
    }
}
