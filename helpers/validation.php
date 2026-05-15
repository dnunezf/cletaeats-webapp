<?php

/**
 * Server-side validation utility.
 */
class Validator
{
    private array $errors = [];

    public function required(string $value, string $field): self
    {
        if (trim($value) === '') {
            $this->errors[$field][] = ucfirst($field) . ' is required.';
        }
        return $this;
    }

    public function email(string $value, string $field): self
    {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'Please enter a valid email address.';
        }
        return $this;
    }

    public function minLength(string $value, int $min, string $field): self
    {
        if ($value !== '' && mb_strlen(trim($value)) < $min) {
            $this->errors[$field][] = ucfirst($field) . " must be at least {$min} characters.";
        }
        return $this;
    }

    public function maxLength(string $value, int $max, string $field): self
    {
        if ($value !== '' && mb_strlen(trim($value)) > $max) {
            $this->errors[$field][] = ucfirst($field) . " must not exceed {$max} characters.";
        }
        return $this;
    }

    public function matches(string $value1, string $value2, string $field): self
    {
        if ($value1 !== $value2) {
            $this->errors[$field][] = ucfirst($field) . ' confirmation does not match.';
        }
        return $this;
    }

    public function phone(string $value, string $field): self
    {
        if ($value !== '' && !preg_match('/^[+]?[\d\s\-()]{7,20}$/', $value)) {
            $this->errors[$field][] = 'Please enter a valid phone number.';
        }
        return $this;
    }

    public function alphanumeric(string $value, string $field): self
    {
        if ($value !== '' && !preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
            $this->errors[$field][] = ucfirst($field) . ' may only contain letters, numbers, and underscores.';
        }
        return $this;
    }

    /**
     * Strong-password rule (single composite check).
     *  - 8..72 chars (bcrypt's input ceiling)
     *  - >= 1 lowercase, 1 uppercase, 1 digit, 1 special character
     *  - no whitespace
     *
     * Skipped silently when $value is empty so it composes with optional update flows
     * (where leaving the field blank means "keep current password"). For required
     * paths, chain ->required($value, $field) before this rule.
     */
    public function password(string $value, string $field = 'password'): self
    {
        if ($value === '') {
            return $this;
        }
        $len = strlen($value);
        if ($len < 8 || $len > 72) {
            $this->errors[$field][] = 'Password must be between 8 and 72 characters.';
            return $this;
        }
        if (preg_match('/\s/', $value)) {
            $this->errors[$field][] = 'Password must not contain spaces.';
            return $this;
        }
        if (!preg_match('/[a-z]/', $value)) {
            $this->errors[$field][] = 'Password must include at least one lowercase letter.';
        }
        if (!preg_match('/[A-Z]/', $value)) {
            $this->errors[$field][] = 'Password must include at least one uppercase letter.';
        }
        if (!preg_match('/\d/', $value)) {
            $this->errors[$field][] = 'Password must include at least one digit.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $this->errors[$field][] = 'Password must include at least one special character.';
        }
        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstErrors(): array
    {
        $first = [];
        foreach ($this->errors as $field => $messages) {
            $first[$field] = $messages[0];
        }
        return $first;
    }
}
