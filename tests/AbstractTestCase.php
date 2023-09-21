<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function setEntityId(object $entity, int $value, $idField = 'id')
    {
        $class = new \ReflectionClass($entity);
        // Get field id
        $property = $class->getProperty($idField);
        // Open this field id
        $property->setAccessible(true);
        // Set value to field id
        $property->setValue($entity, $value);
        // Close this field id
        $property->setAccessible(false);
    }
}
