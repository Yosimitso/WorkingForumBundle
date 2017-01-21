<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ThreadRepository
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 */
class ThreadRepository extends EntityRepository
{
    /**
     * @param integer $start
     * @param integer $limit
     *
     * @return array
     */
    public function getThread($start = 0, $limit = 10)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder
            ->select('a')
            ->addSelect('b')
            ->from($this->_entityName, 'a')
            ->join('YosimitsoWorkingForumBundle:Post', 'b', 'WITH', 'a.id = b.thread')
            ->orderBy('a.note', 'desc')
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return $query->getScalarResult();
    }

    /**
     * @param string  $keywords
     * @param integer $start
     * @param integer $limit
     * @param string  $delimiter
     *
     * @return Thread[]
     */
    public function search($keywords, $start = 0, $limit = 100, $delimiter = ' ')
    {
        $keywords = explode($delimiter, $keywords);
        $where = '';

        foreach ($keywords as $word) {
            $where .= "(a.label LIKE '%" . $word . "%' OR a.subLabel LIKE '%" . $word . "%' OR b.content LIKE '%" . $word . "%') ";
        }

        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder
            ->select('a')
            ->from($this->_entityName, 'a')
            ->join('YosimitsoWorkingForumBundle:Post', 'b', 'WITH', 'a.id = b.thread')
            ->where($where)
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return $query->getResult();
    }
}
