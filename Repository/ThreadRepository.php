<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Doctrine\ORM\Query;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;

/**
 * Class ThreadRepository
 *
 * @package Yosimitso\WorkingForumBundle\Repository
 */
class ThreadRepository extends EntityRepository
{
    /**
     * @param integer $start
     * @param integer $limit
     *
     * @return array
     */
    public function getThread(int $start = 0, int $limit = 10)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder
            ->select('a')
            ->addSelect('b')
            ->from($this->_entityName, 'a')
            ->join(Post::class, 'b', 'WITH', 'a.id = b.thread')
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
    public function search(array $keywords, int $start = 0, int $limit = 100, array $whereSubforum = []) : ?array
    {
        if (empty($whereSubforum)) {
            return null;
        }
        $keywords = explode(' ', $keywords);
        $where = '';

        foreach ($keywords as $word)
        {
            $where .= "(thread.label LIKE '%" . $word . "%' OR thread.subLabel LIKE '%" . $word . "%' OR post.content LIKE '%" . $word . "%') OR";
        }

        $where = rtrim($where, ' OR');

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder
            ->select('thread')
            ->addSelect('subforum')
            ->addSelect('forum')
            ->addSelect('author.avatarUrl AS author_avatarUrl, author.username AS author_username')
            ->addSelect('lastReplyUser.avatarUrl AS lastReplyUser_avatarUrl, lastReplyUser.username AS lastReplyUser_username')
            ->from($this->_entityName, 'thread')
            ->join(Post::class, 'post', 'WITH', 'post.thread = thread.id')
            ->join(UserInterface::class,'author','WITH','thread.author = author.id')
            ->join(UserInterface::class, 'lastReplyUser', 'WITH', 'thread.lastReplyUser = lastReplyUser.id')
            ->join(Subforum::class,'subforum','WITH','thread.subforum = subforum.id')
            ->join(Forum::class, 'forum', 'WITH', 'subforum.forum = forum.id')
            ->where($where)
            ->andWhere('post.moderateReason IS NULL')
            ;

        if (!empty($whereSubforum))
        {
            $queryBuilder->andWhere('subforum.id IN ('.implode(',',$whereSubforum).')');
        }
            $queryBuilder->setMaxResults($limit)
                    
        ;
        $query = $queryBuilder;
        $result = $query->getQuery()->getScalarResult();

        return $result;
    }
    
    public function getAllBySubforum($subforum, $withPosts = false) : array
    {
        $query = $this->_em->createQueryBuilder()
                ->select('thread')
                ->addSelect('subforum')
                ->addSelect('forum')
                ->addSelect('author.avatarUrl AS author_avatarUrl, author.username AS author_username')
                ->addSelect('lastReplyUser.avatarUrl AS lastReplyUser_avatarUrl, lastReplyUser.username AS lastReplyUser_username')
                ->from($this->_entityName, 'thread')
                ->join(UserInterface::class,'author','WITH','thread.author = author.id')
                ->join(UserInterface::class, 'lastReplyUser', 'WITH', 'thread.lastReplyUser = lastReplyUser.id')
                ->join(Subforum::class,'subforum','WITH','thread.subforum = subforum.id')
                ->join(Forum::class, 'forum', 'WITH', 'subforum.forum = forum.id')
                ->where('subforum.id = '.$subforum->getId())
                ->andWhere('thread.slug != :slug_not_empty')
                ->orderBy('thread.pin', 'DESC')
                ->addOrderBy('thread.lastReplyDate', 'DESC')
                ->setParameter('slug_not_empty', '')
            ;
        
        if ($withPosts) {
            $query->addSelect('post')
                ->join(Post::class,'post','WITH','post.thread = thread.id');
        }
        $result = $query->getQuery()->getScalarResult();
        return $result;
    }
}
