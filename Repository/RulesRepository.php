<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Yosimitso\WorkingForumBundle\Entity\Subforum;

/**
 * Class RulesRepository
 *
 * @package Yosimitso\WorkingForumBundle\Repository
 */
class RulesRepository extends EntityRepository
{
    /**
     * @return Subforum[]
     */
    public function getLangs() : ?array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder
            ->select('a.lang')
            ->from('YosimitsoWorkingForumBundle:Rules', 'a')
            ->getQuery()
        ;

        return $query->getResult();
    }
}
