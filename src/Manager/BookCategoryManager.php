<?php

namespace App\Manager;

use App\Entity\BookCategory;
use App\Exception\BookCategoryNotEmptyException;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookCategoryManager implements BookCategoryManagerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookCategoryRepository $bookCategoryRepository
    ) {
    }

    public function getCategories(): BookCategoryListResponse
    {
        $categories = $this->bookCategoryRepository->findAllSortByTitle();

        // The list of categories from entity must be remapped to the list of models.
        $items = array_map(
            fn (BookCategory $bookCategory) => new BookCategoryModel(
                $bookCategory->getId(),
                $bookCategory->getTitle(),
                $bookCategory->getSlug()
            ),
            $categories
        );

        return new BookCategoryListResponse($items);
    }

    public function deleteCategory(int $id): void
    {
        $category = $this->bookCategoryRepository->getById($id);
        $booksCount = $this->bookCategoryRepository->countBooksInCategory($category->getId());
        if ($booksCount > 0) {
            throw new BookCategoryNotEmptyException($booksCount);
        }

        $this->em->remove($category);
        $this->em->flush();
    }
}
