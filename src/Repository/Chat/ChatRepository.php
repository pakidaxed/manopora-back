<?php

declare(strict_types=1);

namespace App\Repository\Chat;

use App\Entity\Chat\Chat;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function save(Chat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Chat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findChatByUsers(User $user1, User $user2): ?Chat
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->where('o.userOne IN (:users) AND o.userTwo IN (:users)')
            ->orWhere('o.userOne IN (:users) AND o.userTwo IN (:users)')
            ->setParameter('users', [$user1, $user2])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findChatListByUser(User $user): ?array
    {
        return $this->createQueryBuilder('o')
            ->select(
                'o.id',
                'CASE WHEN o.userOne = :user THEN two.username ELSE one.username END AS user2',
                'SUM(CASE WHEN message.seen IS NULL OR message.seen = false AND message.sender != :user THEN 1 ELSE 0 END) AS newMessages'
    )
            ->innerJoin('o.userOne', 'one')
            ->innerJoin('o.userTwo', 'two')
            ->leftJoin('o.messages', 'message', 'WITH', 'message.owner = o')
            ->where('o.userOne = :user OR o.userTwo = :user')
            ->setParameter('user', $user)
            ->groupBy('o.id')
            ->orderBy('o.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
