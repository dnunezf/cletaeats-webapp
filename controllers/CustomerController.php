<?php

/**
 * Handles customer CRUD operations.
 */
class CustomerController
{
    private CustomerService $customerService;

    public function __construct()
    {
        $this->customerService = new CustomerService();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $customers = $search !== ''
            ? $this->customerService->search($search)
            : $this->customerService->getAll();

        $pageTitle = 'Customers';
        $currentPage = 'customers';
        view('customers/index', compact('customers', 'pageTitle', 'currentPage', 'search'));
    }

    public function create(): void
    {
        $pageTitle = 'Add Customer';
        $currentPage = 'customers';
        $customer = null;
        $formAction = baseUrl('customers/store');
        view('customers/create', compact('pageTitle', 'currentPage', 'customer', 'formAction'));
    }

    public function store(): void
    {
        csrfCheck();

        $data = $this->extractFormData();

        $result = $this->customerService->create($data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('customers/create');
            return;
        }

        setFlash('success', 'Customer created successfully.');
        redirect('customers');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $customer = $this->customerService->getById($id);

        if (!$customer) {
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }

        $pageTitle = 'Edit Customer';
        $currentPage = 'customers';
        $formAction = baseUrl('customers/update');
        view('customers/edit', compact('pageTitle', 'currentPage', 'customer', 'formAction'));
    }

    public function update(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('customers');
            return;
        }

        $data = $this->extractFormData();

        $result = $this->customerService->update($id, $data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('customers/edit?id=' . $id);
            return;
        }

        setFlash('success', 'Customer updated successfully.');
        redirect('customers');
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            if (isAjax()) {
                jsonResponse(['success' => false, 'message' => 'Invalid customer ID.'], 400);
            }
            redirect('customers');
            return;
        }

        $this->customerService->delete($id);

        if (isAjax()) {
            jsonResponse(['success' => true, 'message' => 'Customer deleted successfully.']);
            return;
        }

        setFlash('success', 'Customer deleted successfully.');
        redirect('customers');
    }

    private function extractFormData(): array
    {
        return [
            'first_name'   => trim($_POST['first_name'] ?? ''),
            'last_name'    => trim($_POST['last_name'] ?? ''),
            'email'        => trim($_POST['email'] ?? ''),
            'phone_number' => trim($_POST['phone_number'] ?? ''),
            'address'      => trim($_POST['address'] ?? ''),
            'city'         => trim($_POST['city'] ?? ''),
            'postal_code'  => trim($_POST['postal_code'] ?? ''),
        ];
    }
}
