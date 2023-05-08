<?php

namespace App\Repository\User;

use App\Entity\Props\City;
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
            ->select('o.name', 'o.birthDate', 'o.description', 'g.name as gender', 'i.name as interest', 'c.name as city')
            ->leftJoin('o.gender', 'g')
            ->leftJoin('o.interest', 'i')
            ->leftJoin('o.city', 'c')
            ->where('o.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllUserProfiles(User $user, int $offset, ?string $city = null): ?array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select(
                'o.id',
                'o.name',
                'o.description',
                'g.name as gender',
                'owner.username',
                'TIMESTAMPDIFF(YEAR, o.birthDate, CURRENT_DATE()) as age',
                'city.title as cityTitle'
            )
            ->leftJoin('o.gender', 'g')
            ->leftJoin('o.interest', 'i')
            ->leftJoin('o.owner', 'owner')
            ->leftJoin('o.city', 'city')
            ->where('i.name = :interest')
            ->andWhere('o.owner != :user')
            ->setParameter('user', $user)
            ->setParameter('interest', $user->getUserProfile()->getInterest()->getName());

        if ($city) {
            $queryBuilder
                ->andWhere('city.name = :city')
                ->setParameter('city', $city);

        }

        $queryBuilder
            ->orderBy('owner.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(10);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findSingleUserProfile(?string $username): ?array
    {
        return $this->createQueryBuilder('o')
            ->select(
                'o.id',
                'o.name',
                'o.description',
                'g.name as gender',
                'owner.username',
                'TIMESTAMPDIFF(YEAR, o.birthDate, CURRENT_DATE()) as age',
                'city.title as cityTitle'
            )
            ->leftJoin('o.gender', 'g')
            ->leftJoin('o.interest', 'i')
            ->leftJoin('o.owner', 'owner')
            ->leftJoin('o.city', 'city')
            ->andWhere('owner.username = :ownerUsername')
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
