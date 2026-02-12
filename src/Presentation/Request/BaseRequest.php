<?php

declare(strict_types=1);

namespace App\Presentation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    public function __construct(protected ValidatorInterface $validator)
    {
        $this->populate();
        $this->validate();
    }

    public function validate(): void
    {
        $violations = $this->validator->validate($this);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $response = new JsonResponse([
                'error' => 'Validation failed',
                'violations' => $errors
            ], JsonResponse::HTTP_BAD_REQUEST);

            $response->send();
            exit;
        }
    }

    protected function populate(): void
    {
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true) ?? [];

        foreach ($data as $property => $value) {
            $camelCase = lcfirst(str_replace('_', '', ucwords($property, '_')));
            if (property_exists($this, $camelCase)) {
                $this->{$camelCase} = $value;
            }
        }
    }

    protected function getQueryParams(): array
    {
        return Request::createFromGlobals()->query->all();
    }
}
