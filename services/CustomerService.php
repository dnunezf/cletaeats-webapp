<?php

/**
 * Customer business logic: CRUD orchestration and validation.
 */
class CustomerService
{
    private CustomerRepository $repo;

    public function __construct()
    {
        $this->repo = new CustomerRepository();
    }

    public function getAll(): array
    {
        return $this->repo->findAllActive();
    }

    public function getById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function search(string $term): array
    {
        $term = trim($term);
        if ($term === '') {
            return $this->getAll();
        }
        return $this->repo->search($term);
    }

    /**
     * Create a customer. Returns the new ID on success or error array on failure.
     */
    public function create(array $data): int|array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $errors;
        }

        if ($data['email'] && $this->repo->findByEmail($data['email'])) {
            return ['email' => 'This email is already registered to another customer.'];
        }

        return $this->repo->create($data);
    }

    /**
     * Update a customer. Returns true on success or error array on failure.
     */
    public function update(int $id, array $data): bool|array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return $errors;
        }

        if ($data['email'] && $this->repo->findByEmailExcluding($data['email'], $id)) {
            return ['email' => 'This email is already registered to another customer.'];
        }

        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data): array
    {
        $validator = new Validator();
        $validator
            ->required($data['first_name'] ?? '', 'first_name')
            ->minLength($data['first_name'] ?? '', 2, 'first_name')
            ->maxLength($data['first_name'] ?? '', 50, 'first_name')
            ->required($data['last_name'] ?? '', 'last_name')
            ->minLength($data['last_name'] ?? '', 2, 'last_name')
            ->maxLength($data['last_name'] ?? '', 50, 'last_name')
            ->required($data['email'] ?? '', 'email')
            ->email($data['email'] ?? '', 'email')
            ->maxLength($data['email'] ?? '', 100, 'email');

        if (!empty($data['phone_number'])) {
            $validator->phone($data['phone_number'], 'phone_number');
        }

        if (!empty($data['postal_code'])) {
            $validator->maxLength($data['postal_code'], 10, 'postal_code');
        }

        return $validator->isValid() ? [] : $validator->getFirstErrors();
    }
}
