<?php

namespace App\Tests;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookChapter;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Entity\Review;
use App\Entity\User;
use App\Model\Author\BookDetails;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookFormat as BookFormatModel;
use Doctrine\Common\Collections\ArrayCollection;

class MockUtils
{
    public static function createUser(): User
    {
        return (new User())
            ->setEmail('vasya@localhost.local')
            ->setFirstName('Vasya')
            ->setLastName('Testerov')
            ->setRoles((array) User::ROLE_AUTHOR)
            ->setPassword('password');
    }

    public static function createBookCategory(): BookCategory
    {
        return (new BookCategory())->setTitle('Devices')->setSlug('devices');
    }

    public static function createBookFormat(): BookFormat
    {
        return (new BookFormat())
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null);
    }

    public static function createBookFormatLink(Book $book, BookFormat $bookFormat): BookToBookFormat
    {
        return (new BookToBookFormat())
            ->setPrice(123.55)
            ->setFormat($bookFormat)
            ->setDiscountPercent(5)
            ->setBook($book);
    }

    public static function createBook(): Book
    {
        return (new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationDate(new \DateTimeImmutable('2020-10-10'))
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([]))
            ->setSlug('test-book');
    }

    public static function createBookChapter(Book $book): BookChapter
    {
        return (new BookChapter())
            ->setTitle('Test chapter')
            ->setBook($book)
            ->setSlug('test-chapter')
            ->setLevel(1)
            ->setSort(1)
            ->setParent(null);
    }

    public static function createReview(Book $book): Review
    {
        return (new Review())
            ->setAuthor('tester')
            ->setContent('test content')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setRating(5)
            ->setBook($book);
    }

    // Create BookDetails
    public static function bookDetails(): BookDetails
    {
        return (new BookDetails())
            ->setId(1)
            ->setTitle('Test book')->setSlug('test-book')
            ->setImage('http://localhost.png')
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationDate(1_602_288_000)
            ->setAuthors(['Tester'])
            ->setCategories([
                new BookCategoryModel(1, 'Devices', 'devices'),
            ])
            ->setFormats([
                (new BookFormatModel())->setId(1)->setTitle('format')
                    ->setDescription('description format')
                    ->setComment(null)
                    ->setPrice(123.55)
                    ->setDiscountPercent(5),
            ]);
    }
}
