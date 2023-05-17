<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\User;
use App\Entity\User\UserPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class UserPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPicture::class);
    }

    public function save(UserPicture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserPicture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllPicturesByOwner(User $user): ?array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.path', 'o.main')
            ->where('o.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult();
    }

    public function findMainImagePath(User $user): ?string
    {
        try {
            $result = $this->createQueryBuilder('o')
                ->select('o.path')
                ->where('o.owner = :owner')
                ->andWhere('o.main = true')
                ->setParameter('owner', $user)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = null;
        }

        return $result;
    }

    public function getAllOtherUserPictures(User $user): ?array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.path')
            ->where('o.owner = :user')
            ->andWhere('o.main = FALSE')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
