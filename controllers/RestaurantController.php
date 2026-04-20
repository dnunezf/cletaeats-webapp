<?php

/**
 * Handles restaurant CRUD operations.
 */
class RestaurantController
{
    private RestaurantService $restaurantService;

    public function __construct()
    {
        $this->restaurantService = new RestaurantService();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $restaurants = $search !== ''
            ? $this->restaurantService->search($search)
            : $this->restaurantService->getAll();

        $pageTitle = 'Restaurants';
        $currentPage = 'restaurants';
        view('restaurants/index', compact('restaurants', 'pageTitle', 'currentPage', 'search'));
    }

    public function create(): void
    {
        $pageTitle = 'Add Restaurant';
        $currentPage = 'restaurants';
        $restaurant = null;
        $formAction = baseUrl('restaurants/store');
        view('restaurants/create', compact('pageTitle', 'currentPage', 'restaurant', 'formAction'));
    }

    public function store(): void
    {
        csrfCheck();

        $data = $this->extractFormData();

        $result = $this->restaurantService->create($data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('restaurants/create');
            return;
        }

        setFlash('success', 'Restaurant created successfully.');
        redirect('restaurants');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $restaurant = $this->restaurantService->getById($id);

        if (!$restaurant) {
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }

        $pageTitle = 'Edit Restaurant';
        $currentPage = 'restaurants';
        $formAction = baseUrl('restaurants/update');
        view('restaurants/edit', compact('pageTitle', 'currentPage', 'restaurant', 'formAction'));
    }

    public function update(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect('restaurants');
            return;
        }

        $data = $this->extractFormData();

        $result = $this->restaurantService->update($id, $data);

        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('restaurants/edit?id=' . $id);
            return;
        }

        setFlash('success', 'Restaurant updated successfully.');
        redirect('restaurants');
    }

    public function delete(): void
    {
        csrfCheck();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            if (isAjax()) {
                jsonResponse(['success' => false, 'message' => 'Invalid restaurant ID.'], 400);
            }
            redirect('restaurants');
            return;
        }

        $this->restaurantService->delete($id);

        if (isAjax()) {
            jsonResponse(['success' => true, 'message' => 'Restaurant deleted successfully.']);
            return;
        }

        setFlash('success', 'Restaurant deleted successfully.');
        redirect('restaurants');
    }

    private function extractFormData(): array
    {
        return [
            'name'              => trim($_POST['name'] ?? ''),
            'legal_id'          => trim($_POST['legal_id'] ?? ''),
            'address'           => trim($_POST['address'] ?? ''),
            'food_type'         => trim($_POST['food_type'] ?? ''),
            'combo_name'        => trim($_POST['combo_name'] ?? ''),
            'combo_description' => trim($_POST['combo_description'] ?? ''),
            'combo_price'       => trim($_POST['combo_price'] ?? ''),
        ];
    }
}
