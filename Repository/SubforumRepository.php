<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Yosimitso\WorkingForumBundle\Entity\Thread;

/**
 * Class SubforumRepository
 *
 * @package Yosimitso\WorkingForumBundle\Repository
 */
class SubforumRepository extends EntityRepository
{
    /**
     * @param int $subforumId
     * @param int $start
     * @param int $limit
     *
     * @return Subforum[]
     */
    public function getListThread($subforumId, $start = 0, $limit = 1)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder
            ->select('a')
            ->from(Thread::class, 'a')
            ->where('a.subforumId = :subforumId')
            ->setParameter('subforumId', $subforumId)
            ->orderBy('a.lastReplyDate', 'desc')
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return $query->getResult();
    }
}
