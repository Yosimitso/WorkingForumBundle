<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;

/**
 * Class SubforumRepository
 *
 * @package Yosimitso\WorkingForumBundle\Repository
 */
class SubforumRepository extends EntityRepository
{
    /**
     * @return Subforum[]
     */
    public function getListThread(int $subforumId, int $start = 0, int $limit = 1)
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
