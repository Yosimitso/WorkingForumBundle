<?php

namespace Yosimitso\WorkingForumBundle\Util;

use Doctrine\ORM\EntityManager;
use Yosimitso\WorkingForumBundle\Util\CacheManager;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;

/**
 * Class EntityProxy
 * @package Yosimitso\WorkingForumBundle\Util
 */
class EntityProxy extends EntityManager
{
    /**
     * @var EntityManager;
     */
    protected $em;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    public function __construct(EntityManager $em, CacheManager $cacheManager)
    {
        $this->em = $em;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Get all forums
     * @return mixed|null
     */
    public function getForums() {
        if ($this->cacheManager->contains('forums')) {
            $listForum = $this->cacheManager->fetch('forum');
        } else {
            $listForum = $this->em
                ->getRepository('Yosimitso\WorkingForumBundle\Entity\Forum')
                ->findAll();
            $this->cacheManager->save('forums', $listForum, CacheManager::TTL_FORUM);
        }

        return $listForum;
    }

    /**
     * Get a subforum
     * @param $slug
     * @return Subforum|null
     */
    public function getSubforum($slug)
    {
        $cacheKey = 'subforum_'.$slug;
        if ($this->cacheManager->contains($cacheKey)) {
            $subforum = $this->cacheManager->fetch($cacheKey);
        } else {
            $subforum = $this
                ->em
                ->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')
                ->findOneBySlug($slug);
            $this->cacheManager->save($cacheKey, $subforum, CacheManager::TTL_FORUM);
        }

        return $subforum;
    }

    /**
     * Get a thread
     * @param $slug
     * @return Thread|null
     */
    public function getThread($slug)
    {
        $cacheKey = 'thread_'.$slug;
        if ($this->cacheManager->contains($cacheKey)) {
            $thread = $this->cacheManager->fetch($cacheKey);
        } else {
            $thread = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($slug);
            $this->cacheManager->save($cacheKey, $thread, CacheManager::TTL_THREAD);
        }

        return $thread;
    }
    /**
     * Get all threads for a subforum
     * @param $subforum
     * @return mixed|null
     */
    public function getThreadsBySubforum($subforum)
    {
        $cacheThreadListKey = 'thread_by_subforum_'.$subforum->getId();

        if ($this->cacheManager->contains($cacheThreadListKey)) {
            $threads = $this->cacheManager->fetch($cacheThreadListKey);
        } else {
            $threads = $this
                ->em
                ->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')
                ->findBySubforum(
                    $subforum->getId(),
                    ['pin' => 'DESC', 'lastReplyDate' => 'DESC']
                );
            $this->cacheManager->save($cacheThreadListKey, $threads, CacheManager::TTL_THREADS_BY_SUBFORUM);
        }

        return $threads;
    }

    /**
     * Search keywords
     * @param $whereSubforum
     * @param $keywords
     * @return mixed|null
     */
    public function search($whereSubforum, $keywords)
    {
        $cacheKey = sha1($whereSubforum.$keywords);

        if ($this->cacheManager->contains($cacheKey)) {
            $threads = $this->cacheManager->fetch($cacheKey);
        } else {
            $threads = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')
                ->search($keywords->getData(), 0, 100, $whereSubforum);
            $this->cacheManager->save($cacheKey, $threads, CacheManager::TTL_SEARCH);
        }

        return $threads;

    }

    
}