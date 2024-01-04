<?php

namespace App\Attribute;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class RequestFile
{
    /**
     * @param Constraint[] $constraints
     */
    public function __construct(private readonly string $field, private readonly array $constraints = [])
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }
}
