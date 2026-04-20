<?php

/**
 * Handles user management operations (admin).
 */
class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $users = $search !== ''
            ? $this->userService->search($search)
            : $this->userService->getAll();

        $pageTitle = 'Users';
        $currentPage = 'users';
        view('users/index', compact('users', 'pageTitle', 'currentPage', 'search'));
    }

    public function pending(): void
    {
        $users = $this->userService->getPendingUsers();
        $pageTitle = 'Pending Users';
        $currentPage = 'pending-users';
        view('users/pending', compact('users', 'pageTitle', 'currentPage'));
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->userService->getById($id);

        if (!$user) {
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }

        $pageTitle = 'Edit User';
        $currentPage = 'users';
        $formAction = baseUrl('users/update');
        view('users/edit', compact('user', 'pageTitle', 'currentPage', 'formAction'));
    }

    public function update(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('users');
            return;
        }

        $data = $this->extractFormData();

        $result = $this->userService->update($id, $data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('users/edit?id=' . $id);
            return;
        }

        setFlash('success', 'User updated successfully.');
        redirect('users');
    }

    public function approve(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('users/pending');
            return;
        }

        $this->userService->approveUser($id);

        setFlash('success', 'User approved successfully.');
        redirect('users/pending');
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        $currentUserId = (int) ($_SESSION['user_id'] ?? 0);

        if ($id <= 0) {
            if (isAjax()) {
                jsonResponse(['success' => false, 'message' => 'Invalid user ID.'], 400);
            }
            redirect('users');
            return;
        }

        $result = $this->userService->delete($id, $currentUserId);

        if (is_array($result)) {
            $message = $result['general'] ?? 'Unable to delete user.';
            if (isAjax()) {
                jsonResponse(['success' => false, 'message' => $message], 400);
            }
            setFlash('errors', ['general' => $message]);
            redirect('users');
            return;
        }

        if (isAjax()) {
            jsonResponse(['success' => true, 'message' => 'User deleted successfully.']);
            return;
        }

        setFlash('success', 'User deleted successfully.');
        redirect('users');
    }

    private function extractFormData(): array
    {
        return [
            'username'         => trim($_POST['username'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'role'             => trim($_POST['role'] ?? ''),
            'status'           => trim($_POST['status'] ?? ''),
            'is_active'        => isset($_POST['is_active']) ? 1 : 0,
            'password'         => (string) ($_POST['password'] ?? ''),
            'password_confirm' => (string) ($_POST['password_confirm'] ?? ''),
        ];
    }
}
