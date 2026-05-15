<?php

/**
 * Self-service profile page for any authenticated user.
 *
 * Auth is enforced at the route level; this controller never trusts the request
 * for the target user id — it always uses currentUserId() from the session.
 * That means a customer cannot edit another customer's profile by manipulating
 * the form, regardless of role.
 */
class ProfileController
{
    private ProfileService $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }

    public function show(): void
    {
        $userId  = (int) (currentUserId() ?? 0);
        $profile = $this->profileService->getProfile($userId);
        if (!$profile) {
            redirect('logout');
            return;
        }

        $pageTitle   = 'Profile Settings';
        $currentPage = 'profile';
        view('profile/index', compact('profile', 'pageTitle', 'currentPage'));
    }

    public function update(): void
    {
        csrfCheck();
        $userId = (int) (currentUserId() ?? 0);
        if ($userId <= 0) {
            redirect('login');
            return;
        }

        $data = [
            'username'         => trim($_POST['username'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'address'          => trim($_POST['address'] ?? ''),
            'city'             => trim($_POST['city'] ?? ''),
            'postal_code'      => trim($_POST['postal_code'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'card_number'      => trim($_POST['card_number'] ?? ''),
            'remove_photo'     => !empty($_POST['remove_photo']),
        ];

        $result = $this->profileService->update($userId, $data, $_FILES);
        if (is_array($result)) {
            setFlash('errors', $result);
            unset($data['password'], $data['password_confirm']);
            setOldInput($data);
            redirect('profile');
            return;
        }
        setFlash('success', 'Profile updated successfully.');
        redirect('profile');
    }
}
