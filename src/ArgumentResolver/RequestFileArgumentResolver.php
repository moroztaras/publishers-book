<?php

namespace App\ArgumentResolver;

use App\Attribute\RequestFile;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestFileArgumentResolver implements ArgumentValueResolverInterface
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return count($argument->getAttributes(RequestFile::class, ArgumentMetadata::IS_INSTANCEOF)) > 0;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @var RequestFile $attribute */
        $attribute = $argument->getAttributes(RequestFile::class, ArgumentMetadata::IS_INSTANCEOF)[0];

        // Get file
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get($attribute->getField());

        // Check validation
        $errors = $this->validator->validate($uploadedFile, $attribute->getConstraints());
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        yield $uploadedFile;
    }
}
