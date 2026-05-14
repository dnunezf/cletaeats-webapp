<?php

class ComplaintService
{
    private ComplaintRepository $repo;
    private OrderRepository $orderRepo;

    public function __construct()
    {
        $this->repo      = new ComplaintRepository();
        $this->orderRepo = new OrderRepository();
    }

    public function getByOrderId(int $orderId): ?array
    {
        return $this->repo->findByOrderId($orderId);
    }

    public function create(int $orderId, string $content, int $rating): bool|array
    {
        $errors = $this->validate($content, $rating);
        if (!empty($errors)) {
            return $errors;
        }
        $order = $this->orderRepo->findById($orderId);
        if (!$order) {
            return ['general' => 'Order not found.'];
        }
        if ($this->repo->findByOrderId($orderId)) {
            return ['general' => 'A complaint already exists for this order.'];
        }
        return $this->repo->create($orderId, trim($content), $rating);
    }

    public function update(int $orderId, string $content, int $rating): bool|array
    {
        $errors = $this->validate($content, $rating);
        if (!empty($errors)) {
            return $errors;
        }
        return $this->repo->update($orderId, trim($content), $rating);
    }

    public function delete(int $orderId): bool
    {
        return $this->repo->delete($orderId);
    }

    private function validate(string $content, int $rating): array
    {
        $errors = [];
        if (trim($content) === '') {
            $errors['content'] = 'Complaint content is required.';
        } elseif (mb_strlen($content) > 255) {
            $errors['content'] = 'Complaint must not exceed 255 characters.';
        }
        if ($rating < 1 || $rating > 5) {
            $errors['rating'] = 'Rating must be between 1 and 5.';
        }
        return $errors;
    }
}
