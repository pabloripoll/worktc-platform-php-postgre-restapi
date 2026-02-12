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
            $this->setProperty($property, $value);
        }
    }

    /**
     * Safely set a property value if it exists
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    private function setProperty(string $property, mixed $value): void
    {
        // Try snake_case first
        if (property_exists($this, $property)) {
            $this->assignValue($property, $value);
            return;
        }

        // Try camelCase
        $camelCase = lcfirst(str_replace('_', '', ucwords($property, '_')));
        if (property_exists($this, $camelCase)) {
            $this->assignValue($camelCase, $value);
        }
    }

    /**
     * Assign value to property using reflection
     *
     * @param string $propertyName
     * @param mixed $value
     * @return void
     */
    private function assignValue(string $propertyName, mixed $value): void
    {
        try {
            $reflection = new \ReflectionProperty($this, $propertyName);
            if ($reflection->isPublic()) {
                $reflection->setValue($this, $value);
            }
        } catch (\ReflectionException) {
            // Property doesn't exist or isn't accessible
        }
    }

    /**
     * Get query parameters from the current request
     *
     * @return array<string, mixed>
     */
    protected function getQueryParams(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return [];
        }

        return $request->query->all();
    }
}
