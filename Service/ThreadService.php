<?php

namespace Yosimitso\WorkingForumBundle\Service;

class ThreadService
{
    protected $em;
    public function __construct($em)
    {
        $this->em = $em;
    }

    public function pin($thread_slug)
    {
        $thread = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new \Exception("Thread error",
                500
            );

        }

        if ($thread->getPin())
        {
            throw new \Exception("Thread already pinned",500);
        }

        $thread->setPin(true);

        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }
}