<?php

namespace App\Tests\Manager;

use App\Entity\BookCategory;
use App\Manager\BookCategoryManager;
use App\Model\BookCategoryListItem;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\Criteria;

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
            ->method('findBy')
            ->with([], ['title' => Criteria::ASC])
            ->willReturn([$category]);

        // Create BookCategoryManager
        $manager = new BookCategoryManager($repository);

        // Expected value
        $expected = new BookCategoryListResponse([new BookCategoryListItem(7, 'Test', 'test')]);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals($expected, $manager->getCategories());
    }
}
