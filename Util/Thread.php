<?php


namespace Yosimitso\WorkingForumBundle\Util;

/**
 * Class Thread
 *
 * @package Yosimitso\WorkingForumBundle\Util
 */
class Thread
{
    private $lockThreadOlderThan;
    private $paginator;
    private $postPerPage;
    private $requestStack;

    public function __construct($lockThreadOlderThan, $paginator, $postPerPage, $requestStack) {
        $this->lockThreadOlderThan = $lockThreadOlderThan;
        $this->paginator = $paginator;
        $this->postPerPage = $postPerPage;
        $this->requestStack = $requestStack;
    }

    /**
     * @param $thread
     * @return bool
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
            $this->requestStack->getCurrentRequest()->query->get('page',1),
            $this->postPerPage
        );
    }
}