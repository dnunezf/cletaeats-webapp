<?php

class ComboController
{
    private ComboService $comboService;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->comboService   = new ComboService();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $restaurantId = (int) ($_GET['restaurant_id'] ?? 0);

        // Restaurants only ever see their own combos, regardless of GET params.
        if (userIsRestaurant()) {
            $restaurantId = (int) (currentUserId() ?? 0);
        }

        if ($restaurantId > 0) {
            $combos = $search !== ''
                ? $this->comboService->searchByRestaurant($restaurantId, $search)
                : $this->comboService->getByRestaurant($restaurantId);
            $restaurant = $this->restaurantRepo->findById($restaurantId);
        } else {
            $combos = $search !== '' ? $this->comboService->search($search) : $this->comboService->getAll();
            $restaurant = null;
        }

        $pageTitle = 'Combos';
        $currentPage = 'combos';
        view('combos/index', compact('combos', 'restaurant', 'pageTitle', 'currentPage', 'search'));
    }

    public function create(): void
    {
        $restaurantId = (int) ($_GET['restaurant_id'] ?? 0);
        // For the restaurant role, the picker is always their own user_id.
        if (userIsRestaurant()) {
            $restaurantId = (int) (currentUserId() ?? 0);
            $restaurants  = [];
        } else {
            $restaurants  = $this->restaurantRepo->findAll();
        }
        $combo        = null;
        $formAction   = baseUrl('combos/store');

        $pageTitle = 'Add Combo';
        $currentPage = 'combos';
        view('combos/create', compact('combo', 'restaurants', 'restaurantId', 'formAction', 'pageTitle', 'currentPage'));
    }

    public function store(): void
    {
        csrfCheck();
        $data = $this->extractFormData();
        // Restaurants can only create combos under their own restaurant.
        if (userIsRestaurant()) {
            $data['restaurant_id'] = (int) (currentUserId() ?? 0);
        }
        $result = $this->comboService->create($data);
        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('combos/create');
            return;
        }
        setFlash('success', 'Combo created successfully.');
        redirect('combos');
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $combo = $this->comboService->getById($id);
        if (!$combo || !$this->canManageCombo($combo)) {
            http_response_code(404);
            require BASE_PATH . '/views/errors/404.php';
            exit;
        }
        $restaurants = userIsRestaurant() ? [] : $this->restaurantRepo->findAll();
        $formAction  = baseUrl('combos/update');
        $pageTitle   = 'Edit Combo';
        $currentPage = 'combos';
        view('combos/edit', compact('combo', 'restaurants', 'formAction', 'pageTitle', 'currentPage'));
    }

    public function update(): void
    {
        csrfCheck();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) { redirect('combos'); return; }

        // Restaurant role: must own the combo, and cannot reassign it elsewhere.
        if (userIsRestaurant()) {
            if (!$this->comboService->getById($id)
                || !$this->ownsCombo($id)) {
                http_response_code(404);
                require BASE_PATH . '/views/errors/404.php';
                exit;
            }
        }

        $data = $this->extractFormData();
        if (userIsRestaurant()) {
            $data['restaurant_id'] = (int) (currentUserId() ?? 0);
        }
        $result = $this->comboService->update($id, $data);
        if (is_array($result)) {
            setFlash('errors', $result);
            setOldInput($data);
            redirect('combos/edit?id=' . $id);
            return;
        }
        setFlash('success', 'Combo updated successfully.');
        redirect('combos');
    }

    public function delete(): void
    {
        csrfCheck();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            if (isAjax()) { jsonResponse(['success' => false, 'message' => 'Invalid combo ID.'], 400); }
            redirect('combos');
            return;
        }
        if (userIsRestaurant() && !$this->ownsCombo($id)) {
            if (isAjax()) { jsonResponse(['success' => false, 'message' => 'Forbidden.'], 403); }
            http_response_code(403);
            require BASE_PATH . '/views/errors/403.php';
            exit;
        }
        $this->comboService->delete($id);
        if (isAjax()) { jsonResponse(['success' => true, 'message' => 'Combo deleted successfully.']); return; }
        setFlash('success', 'Combo deleted successfully.');
        redirect('combos');
    }

    private function extractFormData(): array
    {
        return [
            'restaurant_id' => (int) ($_POST['restaurant_id'] ?? 0),
            'name'          => trim($_POST['name'] ?? ''),
            'description'   => trim($_POST['description'] ?? ''),
            'price'         => trim($_POST['price'] ?? ''),
        ];
    }

    /** Admin always; restaurant only if it owns the combo. */
    private function canManageCombo(array $combo): bool
    {
        if (userIsAdmin()) {
            return true;
        }
        if (userIsRestaurant()) {
            return (int) ($combo['restaurant_id'] ?? 0) === (int) (currentUserId() ?? 0);
        }
        return false;
    }

    private function ownsCombo(int $comboId): bool
    {
        $repo = new ComboRepository();
        return $repo->isOwnedByRestaurant($comboId, (int) (currentUserId() ?? 0));
    }
}
