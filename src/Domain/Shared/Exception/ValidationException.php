<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

class ValidationException extends DomainException
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create from array of validation errors
     * @param array<string, string> $errors
     */
    public static function fromErrors(array $errors): self
    {
        $messages = [];
        foreach ($errors as $field => $error) {
            $messages[] = "{$field}: {$error}";
        }

        return new self(implode(', ', $messages));
    }

    /**
     * Create for a single field
     */
    public static function forField(string $field, string $message): self
    {
        return new self("{$field}: {$message}");
    }
}
