<?php

namespace App\Tests\ArgumentResolver;

use App\ArgumentResolver\RequestBodyArgumentResolver;
use App\Attribute\RequestBody;
use App\Exception\RequestBodyConvertException;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyArgumentResolverTest extends AbstractTestCase
{
    private SerializerInterface $serializer;

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock  dependents
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    // Test for check Request and ArgumentMetadata
    public function testSupports(): void
    {
        // Expected metadata
        $meta = new ArgumentMetadata('some', null, false, false, null, false, attributes: [
            new RequestBody(),
        ]);

        // Comparing the actual returned response with the expected metadata.
        $this->assertTrue($this->createResolver()->supports(new Request(), $meta));
    }

    // Test for check Request and ArgumentMetadata without attributes
    public function testNotSupports(): void
    {
        // Expected metadata
        $meta = new ArgumentMetadata('some', null, false, false, null);

        // Comparing the actual returned response with the expected metadata.
        $this->assertFalse($this->createResolver()->supports(new Request(), $meta));
    }

    public function testResolveThrowsWhenDeserialize(): void
    {
        // Expect exception
        $this->expectException(RequestBodyConvertException::class);

        // Create request
        $request = new Request([], [], [], [], [], [], 'testing content');

        // Create meta data
        $meta = new ArgumentMetadata('some', \stdClass::class, false, false, null, false, [
            new RequestBody(),
        ]);

        // Set the behavior of the method deserialize
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with('testing content', \stdClass::class, JsonEncoder::FORMAT)
            ->willThrowException(new \Exception());

        // Run method resolve
        $this->createResolver()->resolve($request, $meta)->next();
    }

    // Helper method for create resolver
    private function createResolver(): RequestBodyArgumentResolver
    {
        return new RequestBodyArgumentResolver($this->serializer, $this->validator);
    }
}
