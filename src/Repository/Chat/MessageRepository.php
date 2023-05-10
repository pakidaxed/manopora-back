<?php

declare(strict_types=1);

namespace App\Repository\Chat;

use App\Entity\Chat\Chat;
use App\Entity\Chat\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllMessages(Chat $chat): ?array
    {
        return $this->createQueryBuilder('o')
            ->select('o.id', 'o.message', 'o.seen', 'o.createdAt', 'sender.username')
            ->leftJoin('o.owner', 'chat')
            ->leftJoin('o.sender', 'sender')
            ->where('o.owner = :owner')
            ->setParameter('owner', $chat)
            ->orderBy('o.createdAt', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }
}
