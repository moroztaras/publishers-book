<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BookContent;
use App\Exception\BookChapterContentNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookContent[]    findAll()
 * @method BookContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookContentRepository extends ServiceEntityRepository
{
    use RepositoryModifyTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookContent::class);
    }

    public function getById(int $id): BookContent
    {
        $chapter = $this->find($id);
        if (null === $chapter) {
            throw new BookChapterContentNotFoundException();
        }

        return $chapter;
    }
}
