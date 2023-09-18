<?php

namespace App\Tests\Manager;

use App\Entity\BookCategory;
use App\Manager\BookCategoryManager;
use App\Model\BookCategoryListItem;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class BookCategoryManagerTest extends TestCase
{
    // Unit test
    public function testGetCategories(): void
    {
        // Mock BookCategoryRepository
        $repository = $this->createMock(BookCategoryRepository::class);

        // Set the behavior of BookCategoryRepository
        $repository->expects($this->once())
            ->method('findBy')
            ->with([], ['title' => Criteria::ASC])
            ->willReturn([(new BookCategory())->setId(7)->setTitle('Test')->setSlug('test')]);

        // Create BookCategoryManager
        $manager = new BookCategoryManager($repository);

        // Expected value
        $expected = new BookCategoryListResponse([new BookCategoryListItem(7, 'Test', 'test')]);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals($expected, $manager->getCategories());
    }
}
