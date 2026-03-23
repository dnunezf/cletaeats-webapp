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

    public function pending(): void
    {
        $users = $this->userService->getPendingUsers();
        $pageTitle = 'Pending Users';
        view('users/pending', compact('users', 'pageTitle'));
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
}
