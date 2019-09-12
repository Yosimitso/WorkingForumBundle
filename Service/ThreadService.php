<?php

namespace Yosimitso\WorkingForumBundle\Service;

use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Util\Slugify;

class ThreadService
{
    private $lockThreadOlderThan;
    private $paginator;
    private $postPerPage;
    private $requestStack;
    protected $em;
    protected $user;

    public function __construct($lockThreadOlderThan, $paginator, $postPerPage, $requestStack, $em, $user)
    {
        $this->lockThreadOlderThan = $lockThreadOlderThan;
        $this->paginator = $paginator;
        $this->postPerPage = $postPerPage;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * @param $thread
     * @return bool
     * @throws \Exception
     * Is the thread autolocked ?
     */
    public function isAutolock($thread)
    {
        if ($this->lockThreadOlderThan) {
            $diff = $thread->getLastReplyDate()->diff(new \DateTime());
            if ($diff->days > $this->lockThreadOlderThan) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * @param $postQuery
     * @return mixed
     * Return the post list according to pagination parameters and query
     */
    public function paginate($postQuery)
    {
        return $this->paginator->paginate(
            $postQuery,
            $this->requestStack->getCurrentRequest()->query->get('page', 1),
            $this->postPerPage
        );
    }

    /**
     * @param Thread $thread
     * @return string
     * Generates a slug for a thread
     */
    public function slugify(Thread $thread)
    {
        return $thread->getId().'-'.Slugify::convert($thread->getLabel());
    }

    public function pin(Thread $thread)
    {
        $thread->setPin(true);

        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * @param Thread $thread
     * @return bool
     * Resolve thread
     */
    public function resolve(Thread $thread)
    {
        $thread->setResolved(true);
        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * @param Thread $thread
     * @return bool
     * Lock thread
     */
    public function lock(Thread $thread)
    {
        $thread->setLocked(true);
        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * @param Post $post
     * @return bool
     * Report a thread
     */
    public function report(Post $post)
    {
        if (!is_null($post) && empty($post->getModerateReason()) && !is_null($this->user)) // THE POST EXISTS AND IS "VISIBLE"
        {
            $report = new PostReport;
            $report->setPost($post)
                ->setUser($this->user);
            $this->em->persist($report);
            $this->em->flush();

            return true;
        } else {

            return false;
        }
    }

    /**
     * @param Thread $thread
     * @param Subforum $currentSubforum
     * @param Subforum $targetSubforum
     * @return bool
     * Move thread to an another subforum
     */
    public function moveThread(Thread $thread, Subforum $currentSubforum, Subforum $targetSubforum)
    {
        $currentSubforum->setNbThread($currentSubforum->getNbThread() - 1);
        $currentSubforum->setNbPost($currentSubforum->getNbPost() - $thread->getNbReplies());

        $thread->setSubforum($targetSubforum);

        $targetSubforum->setNbThread($targetSubforum->getNbThread() + 1);
        $targetSubforum->setNbPost($targetSubforum->getNbPost() + $thread->getNbReplies());

        $this->em->persist($thread);
        $this->em->persist($currentSubforum);
        $this->em->persist($targetSubforum);
        $this->em->flush();

        return true;
    }


}