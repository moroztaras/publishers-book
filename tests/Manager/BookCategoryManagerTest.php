<?php

namespace App\Tests\Manager;

use App\Entity\BookCategory;
use App\Manager\BookCategoryManager;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Tests\AbstractTestCase;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookCategoryManagerTest extends AbstractTestCase
{
    // Unit test
    public function testGetCategories(): void
    {
        $category = (new BookCategory())->setTitle('Test')->setSlug('test');
        // Set id for category
        $this->setEntityId($category, 7);

        // Mock BookCategoryRepository
        $repository = $this->createMock(BookCategoryRepository::class);

        // Set the behavior of BookCategoryRepository
        $repository->expects($this->once())
            ->method('findAllSortByTitle')
            ->willReturn([$category]);

        // Mock for slugger
        $slugger = $this->createMock(SluggerInterface::class);
        // Create BookCategoryManager
        $manager = new BookCategoryManager($repository, $slugger);

        // Expected value
        $expected = new BookCategoryListResponse([new BookCategoryModel(7, 'Test', 'test')]);

        // Comparing the expected value with the actual returned value
        $this->assertEquals($expected, $manager->getCategories());
    }
}
