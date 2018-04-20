<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class RulesRepository
 *
 * @package Yosimitso\WorkingForumBundle\Repository
 */
class RulesRepository extends EntityRepository
{
    /**
     * @param int $subforumId
     * @param int $start
     * @param int $limit
     *
     * @return Subforum[]
     */
    public function getLangs()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder
            ->select('a.lang')
            ->from('YosimitsoWorkingForumBundle:Rules', 'a')
            ->getQuery()
        ;
//        exit(dump($query->getResult()));
        return $query->getResult();
    }
}
