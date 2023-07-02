<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\User;
use App\Entity\User\UserProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
                'i.interestTitle as interest',
                'owner.username',
                'TIMESTAMPDIFF(YEAR, o.birthDate, CURRENT_DATE()) as age',
                'city.title as cityTitle',
                'userPicture.path as userMainPicture',
            )
            ->leftJoin('o.gender', 'g')
            ->leftJoin('o.interest', 'i')
            ->leftJoin('o.owner', 'owner')
            ->leftJoin('owner.userPictures', 'userPicture', 'WITH', 'userPicture.owner = owner AND userPicture.main = true')
            ->leftJoin('o.city', 'city');

        if ($user->hasUserProfile()) {
            $queryBuilder
                ->where('g.name = :interest')
                ->setParameter('interest', $user->getUserProfile()->getInterest()->getName() ?? '');
        }

        $queryBuilder
            ->andWhere('o.owner != :user')
            ->setParameter('user', $user);

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

    public function findSingleUserProfile(User $user): ?array
    {
        return $this->createQueryBuilder('o')
            ->select(
                'o.id',
                'o.name',
                'o.description',
                'g.name as gender',
                'i.interestTitle as interest',
                'owner.username',
                'TIMESTAMPDIFF(YEAR, o.birthDate, CURRENT_DATE()) as age',
                'city.title as cityTitle',
                'CASE WHEN userPicture.main = true THEN userPicture.path ELSE :null END as userMainPicture',
            )
            ->leftJoin('o.gender', 'g')
            ->leftJoin('o.interest', 'i')
            ->leftJoin('o.owner', 'owner')
            ->leftJoin('o.city', 'city')
            ->leftJoin('owner.userPictures', 'userPicture', 'WITH', 'userPicture.owner = owner')
            ->andWhere('o.owner = :user')
            ->setParameter('user', $user)
            ->setParameter('null', NULL)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
