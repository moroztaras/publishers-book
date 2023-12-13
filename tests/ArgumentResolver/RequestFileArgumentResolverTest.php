<?php

namespace App\Tests\ArgumentResolver;

use App\ArgumentResolver\RequestFileArgumentResolver;
use App\Attribute\RequestFile;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestFileArgumentResolverTest extends AbstractTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    // When pass a constraint
    public function testSupports(): void
    {
        $meta = new ArgumentMetadata('some', null, false, false, null, false, [
            new RequestFile('file', []),
        ]);

        $this->assertTrue($this->createResolver()->supports(new Request(), $meta));
    }

    private function createResolver(): RequestFileArgumentResolver
    {
        return new RequestFileArgumentResolver($this->validator);
    }
}
