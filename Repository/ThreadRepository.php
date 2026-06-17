<?php

namespace Yosimitso\WorkingForumBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Doctrine\ORM\Query;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;
use Yosimitso\WorkingForumBundle\Service\BundleParametersService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private BundleParametersService $bundleParametersService)
    {
        parent::__construct($registry, Thread::class);
    }
    
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
     * @return Thread[]
     */
    public function search(array $keywords, int $start = 0, int $limit = 100, array $whereSubforum = []) : ?array
    {
        if (empty($whereSubforum)) {
            return null;
        }

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
    
    public function getAllBySubforumAsScalar($subforum, $withPosts = false) : array
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

    public function getAllBySubforum(SubForum $subforum, int $page) : array
    {
        $query = $this->_em->createQueryBuilder()
            ->select('thread')
            ->from($this->_entityName, 'thread')
            ->where('thread.subforum = :subForumId')
            ->andWhere('thread.slug != :slug_not_empty')
            ->setFirstResult(($page * $this->bundleParametersService->get('thread_per_page')) - 1)
            ->setMaxResults($this->bundleParametersService->get('thread_per_page'))
            ->orderBy('thread.pin', 'DESC')
            ->addOrderBy('thread.lastReplyDate', 'DESC')
            ->setParameter('subForumId', $subforum->getId())
            ->setParameter('slug_not_empty', '')
        ;
        
        $result = $query->getQuery()->getResult();

        return $result;
    }

    public function countAllBySubforum(SubForum $subforum) : int
    {
        $query = $this->_em->createQueryBuilder()
            ->select('COUNT(thread)')
            ->from($this->_entityName, 'thread')
            ->where('thread.subforum = :subForumId')
            ->andWhere('thread.slug != :slug_not_empty')
            ->setParameter('subForumId', $subforum->getId())
            ->setParameter('slug_not_empty', '')
        ;

        $result = $query->getQuery()->getSingleScalarResult();

        return $result;
    }
}
