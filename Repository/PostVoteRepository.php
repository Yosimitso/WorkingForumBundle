<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;

/**
 * Class PostVoteRepository
 *
 * @package Yosimitso\WorkingForumBundle\Repository
 */
class PostVoteRepository extends EntityRepository
{
    public function getThreadVoteByUser(Thread $thread, UserInterface $user) : array
    {
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
