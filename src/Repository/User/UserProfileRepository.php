<?php

namespace App\Repository\User;

use App\Entity\User\User;
use App\Entity\User\UserProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserProfile>
 *
 * @method UserProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProfile[]    findAll()
 * @method UserProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProfile::class);
    }

    public function save(UserProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOneByOwner(User $user): ?array
    {
        return $this->createQueryBuilder('o')
            ->select('o.name', 'o.birthDate', 'o.description' , 'g.name as gender', 'i.name as interest')
            ->leftJoin('o.gender', 'g')
            ->leftJoin('o.interest', 'i')
            ->where('o.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllUserProfiles(int $offset): ?array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.name', 'o.description', 'g.name as gender', 'owner.username')
            ->leftJoin('o.gender', 'g')
            ->leftJoin('o.interest', 'i')
            ->leftJoin('o.owner', 'owner')
            ->where('i.name = :interest')
            ->setParameter('interest', 'moteris') // TODO PAKEISTI IS PROFILIO
            ->setFirstResult($offset)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findSingleUserProfile(?string $username): ?array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.name', 'o.description', 'g.name as gender', 'owner.username')
            ->leftJoin('o.gender', 'g')
            ->leftJoin('o.interest', 'i')
            ->leftJoin('o.owner', 'owner')
            ->where('i.name = :interest')
            ->andWhere('owner.username = :ownerUsername')
            ->setParameter('interest', 'moteris')
            ->setParameter('ownerUsername', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return UserProfile[] Returns an array of UserProfile objects
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

//    public function findOneBySomeField($value): ?UserProfile
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}