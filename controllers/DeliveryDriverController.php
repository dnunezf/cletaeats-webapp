<?php

/**
 * Handles delivery driver CRUD operations.
 */
class DeliveryDriverController
{
    private DeliveryDriverService $driverService;

    public function __construct()
    {
        $this->driverService = new DeliveryDriverService();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $drivers = $search !== ''
            ? $this->driverService->search($search)
            : $this->driverService->getAll();

        $pageTitle = 'Delivery Drivers';
        $currentPage = 'drivers';
        view('drivers/index', compact('drivers', 'pageTitle', 'currentPage', 'search'));
    }

    public function create(): void
    {
        $pageTitle = 'Add Delivery Driver';
        $currentPage = 'drivers';
        $driver = null;
        $formAction = baseUrl('drivers/store');
        view('drivers/create', compact('pageTitle', 'currentPage', 'driver', 'formAction'));
    }

    public function store(): void
    {
        csrfCheck();

        $data = $this->extractFormData();

        $result = $this->driverService->create($data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('drivers/create');
            return;
        }

        setFlash('success', 'Delivery driver created successfully.');
        redirect('drivers');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $driver = $this->driverService->getById($id);

        if (!$driver) {
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }

        $pageTitle = 'Edit Delivery Driver';
        $currentPage = 'drivers';
        $formAction = baseUrl('drivers/update');
        view('drivers/edit', compact('pageTitle', 'currentPage', 'driver', 'formAction'));
    }

    public function update(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('drivers');
            return;
        }

        $data = $this->extractFormData();

        $result = $this->driverService->update($id, $data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('drivers/edit?id=' . $id);
            return;
        }

        setFlash('success', 'Delivery driver updated successfully.');
        redirect('drivers');
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            if (isAjax()) {
                jsonResponse(['success' => false, 'message' => 'Invalid driver ID.'], 400);
            }
            redirect('drivers');
            return;
        }

        $this->driverService->delete($id);

        if (isAjax()) {
            jsonResponse(['success' => true, 'message' => 'Delivery driver deleted successfully.']);
            return;
        }

        setFlash('success', 'Delivery driver deleted successfully.');
        redirect('drivers');
    }

    private function extractFormData(): array
    {
        return [
            'full_name'           => trim($_POST['full_name'] ?? ''),
            'id_number'           => trim($_POST['id_number'] ?? ''),
            'email'               => trim($_POST['email'] ?? ''),
            'address'             => trim($_POST['address'] ?? ''),
            'phone'               => trim($_POST['phone'] ?? ''),
            'card_number'         => trim($_POST['card_number'] ?? ''),
            'status'              => trim($_POST['status'] ?? 'available'),
            'order_distance'      => trim($_POST['order_distance'] ?? ''),
            'daily_kilometers'    => trim($_POST['daily_kilometers'] ?? ''),
            'weekday_cost_per_km' => trim($_POST['weekday_cost_per_km'] ?? ''),
            'holiday_cost_per_km' => trim($_POST['holiday_cost_per_km'] ?? ''),
            'warning_count'       => trim($_POST['warning_count'] ?? '0'),
            'complaints'          => trim($_POST['complaints'] ?? ''),
        ];
    }
}
