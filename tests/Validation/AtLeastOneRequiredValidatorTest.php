<?php

namespace App\Tests\Validation;

use App\Validation\AtLeastOneRequired;
use App\Validation\AtLeastOneRequiredValidator;
use stdClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class AtLeastOneRequiredValidatorTest extends ConstraintValidatorTestCase
{
    private PropertyAccessorInterface $propertyAccessor;

    protected function setUp(): void
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();

        parent::setUp();
    }

    public function testValidateExceptionOnUnexpectedType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate([], new NotNull());
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new AtLeastOneRequiredValidator($this->propertyAccessor);
    }
}
