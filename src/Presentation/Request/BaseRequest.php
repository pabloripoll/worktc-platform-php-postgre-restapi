<?php

declare(strict_types=1);

namespace App\Presentation\Request;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    use ValidatableRequestTrait;

    public function __construct(
        protected ValidatorInterface $validator,
        protected RequestStack $requestStack
    ) {
        $this->populate();
    }

    protected function populate(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return;
        }

        $data = json_decode($request->getContent(), true) ?? [];

        foreach ($data as $property => $value) {
            // Try snake_case first (matches your property names)
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            } else {
                // Fallback to camelCase conversion for backward compatibility
                $camelCase = lcfirst(str_replace('_', '', ucwords($property, '_')));
                if (property_exists($this, $camelCase)) {
                    $this->{$camelCase} = $value;
                }
            }
        }
    }

    protected function getQueryParams(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return [];
        }

        return $request->query->all();
    }
}
