<?php

declare(strict_types=1);

namespace App\Presentation\Request;

use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatableRequestTrait
{
    /**
     * Validate the request and return errors array
     *
     * @return array<string, string> Empty array if valid, otherwise property => error message
     */
    public function validate(): array
    {
        if (!isset($this->validator) || !$this->validator instanceof ValidatorInterface) {
            throw new \RuntimeException('Validator not available. Make sure the class has a $validator property.');
        }

        $violations = $this->validator->validate($this);

        if (count($violations) === 0) {
            return [];
        }

        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}
