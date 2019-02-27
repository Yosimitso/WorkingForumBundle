<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CensorshipRepository
 *
 * @package Yosimitso\WorkingForumBundle\Repository
 */
class CensorshipRepository extends EntityRepository
{

    public function getCensoredPatterns()
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder
            ->select('a.pattern')
            ->from('YosimitsoWorkingForumBundle:Censorship', 'a')
            ->getQuery()
        ;
        $queryResults = $query->getResult();
        $results = array_column($queryResults, 'pattern');
        foreach ($results as $index => $result) {
            $results[$index] = '/'.$result.'/';
        }
        return $results;
    }
}
