<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class SubforumRepository
 *
 * @package Yosimitso\WorkingForumBundle\Entity
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
            ->from('YosimitsoWorkingForumBundle:Thread', 'a')
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
