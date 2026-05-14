<?php

class ComboService
{
    private ComboRepository $repo;
    private RestaurantRepository $restaurantRepo;

    public function __construct()
    {
        $this->repo           = new ComboRepository();
        $this->restaurantRepo = new RestaurantRepository();
    }

    public function getAll(): array
    {
        return $this->repo->findAll();
    }

    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function getByRestaurant(int $restaurantId): array
    {
        return $this->repo->findAllByRestaurant($restaurantId);
    }

    public function search(string $term): array
    {
        $term = trim($term);
        return $term === '' ? $this->getAll() : $this->repo->search($term);
    }

    public function create(array $data): int|array
    {
        $errors = $this->validate($data, isCreate: true);
        if (!empty($errors)) {
            return $errors;
        }
        $restaurantId = (int) $data['restaurant_id'];
        if (!$this->restaurantRepo->findById($restaurantId)) {
            return ['restaurant_id' => 'Restaurant not found.'];
        }
        return $this->repo->create(
            $restaurantId,
            trim($data['name']),
            trim($data['description']),
            (float) $data['price']
        );
    }

    public function update(int $id, array $data): bool|array
    {
        $existing = $this->repo->findById($id);
        if (!$existing) {
            return ['general' => 'Combo not found.'];
        }
        $errors = $this->validate($data, isCreate: false);
        if (!empty($errors)) {
            return $errors;
        }
        return $this->repo->update(
            $id,
            trim($data['name']),
            trim($data['description']),
            (float) $data['price']
        ) ? true : ['general' => 'Unable to update combo.'];
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data, bool $isCreate): array
    {
        $v = new Validator();
        $v->required($data['name'] ?? '', 'name')
          ->minLength($data['name'] ?? '', 2, 'name')
          ->maxLength($data['name'] ?? '', 255, 'name')
          ->required($data['description'] ?? '', 'description')
          ->maxLength($data['description'] ?? '', 255, 'description');

        if ($isCreate) {
            $rid = $data['restaurant_id'] ?? '';
            if (!preg_match('/^\d+$/', (string) $rid) || (int) $rid <= 0) {
                $v->required('', 'restaurant_id');
            }
        }

        $errors = $v->isValid() ? [] : $v->getFirstErrors();

        $price = $data['price'] ?? '';
        if (trim((string) $price) === '' || !is_numeric($price) || (float) $price < 0 || (float) $price > 999999.99) {
            $errors['price'] = 'Price must be a non-negative number up to 999999.99.';
        }

        return $errors;
    }
}
