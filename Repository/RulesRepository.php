<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Yosimitso\WorkingForumBundle\Entity\Rules;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class RulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rules::class);
    }
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
