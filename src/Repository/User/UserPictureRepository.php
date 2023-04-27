<?php

namespace App\Repository\User;

use App\Entity\User\User;
use App\Entity\User\UserPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPicture>
 *
 * @method UserPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPicture[]    findAll()
 * @method UserPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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

//    /**
//     * @return UserPicture[] Returns an array of UserPicture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserPicture
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
