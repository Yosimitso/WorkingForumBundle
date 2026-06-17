<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Yosimitso\WorkingForumBundle\Entity\PostVote;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class PostVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostVote::class);
    }

    public function getThreadVoteByUser(Thread $thread, ?UserInterface $user) : array
    {
        if (is_null($user)) {
            return [];
        }
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder
            ->select('(a.post)')
            ->from('YosimitsoWorkingForumBundle:PostVote', 'a')
            ->join('YosimitsoWorkingForumBundle:Thread', 'b', 'WITH', 'a.thread = b.id')
            ->where('a.thread = :thread')
            ->andWhere('a.user = :user')
            ->setParameter('thread', $thread)
            ->setParameter('user', $user)
            ->getQuery()
        ;
        $queryResults = $query->getResult();
        return array_column($queryResults, '1');
    }
}
