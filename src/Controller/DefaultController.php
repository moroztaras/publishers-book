<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// TODO This controller only for test. DefaultController need remove in future.
class DefaultController extends AbstractController
{
    public function __construct(
        private BookRepository $bookRepository,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/test-new-book', methods: 'GET')]
    public function testNewBook(): Response
    {
        $book = (new Book())->setTitle('New title book');
        $this->em->persist($book);
        $this->em->flush();

        return $this->json($book);
    }

    #[Route('/test-book', methods: 'GET')]
    public function testBook(): Response
    {
        $books = $this->bookRepository->findAll();

        return $this->json($books);
    }

    #[Route(path: '/root', methods: 'GET')]
    public function root(): Response
    {
        return $this->json(['hello' => 'world!']);
    }
}
