<?php

/**
 * Handles login, registration, and logout actions.
 */
class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showLogin(): void
    {
        view('auth/login', [], 'auth');
    }

    public function showCreateAccount(): void
    {
        view('auth/create-account', [], 'auth');
    }

    public function createAccount(): void
    {
        csrfCheck();

        $data = [
            'username'         => trim($_POST['username'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
        ];

        $result = $this->authService->createAccount($data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('create-account');
            return;
        }

        setFlash('success', 'Your account has been created and is pending approval by an administrator.');
        redirect('login');
    }

    public function login(): void
    {
        csrfCheck();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Server-side validation
        $validator = new Validator();
        $validator
            ->required($username, 'username')
            ->required($password, 'password');

        if (!$validator->isValid()) {
            setFlash('errors', $validator->getFirstErrors());
            setOldInput(['username' => $username]);
            redirect('login');
            return;
        }

        $result = $this->authService->authenticate($username, $password);

        if (is_string($result)) {
            setFlash('error', $result);
            setOldInput(['username' => $username]);
            redirect('login');
            return;
        }

        // Successful login - regenerate session ID
        session_regenerate_id(true);

        $_SESSION['user_id'] = $result->id;
        $_SESSION['username'] = $result->username;
        $_SESSION['role'] = $result->role;

        redirect('dashboard');
    }

    public function showRegister(): void
    {
        view('auth/register');
    }

    public function register(): void
    {
        csrfCheck();

        $data = [
            'username'         => trim($_POST['username'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'role'             => $_POST['role'] ?? 'user',
        ];

        // Validate role value
        if (!in_array($data['role'], ['admin', 'user'], true)) {
            $data['role'] = 'user';
        }

        $result = $this->authService->register($data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('register');
            return;
        }

        setFlash('success', 'User registered successfully.');
        redirect('register');
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        redirect('login');
    }
}
