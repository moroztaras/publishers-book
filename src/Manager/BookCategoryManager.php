<?php

namespace App\Manager;

use App\Entity\BookCategory;
use App\Model\BookCategoryListItem;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use Doctrine\Common\Collections\Criteria;

class BookCategoryManager implements BookCategoryManagerInterface
{
    public function __construct(private BookCategoryRepository $bookCategoryRepository)
    {
    }

    public function getCategories(): BookCategoryListResponse
    {
        $categories = $this->bookCategoryRepository->findBy([], ['title' => Criteria::ASC]);

        // The list of categories from entity must be remapped to the list of models.
        $items = array_map(
            fn (BookCategory $bookCategory) => new BookCategoryListItem(
                $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug()
            ),
            $categories
        );

        return new BookCategoryListResponse($items);
    }
}
