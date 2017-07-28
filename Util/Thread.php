<?php


namespace Yosimitso\WorkingForumBundle\Util;

/**
 * Class Slugify
 *
 * @package Yosimitso\WorkingForumBundle\Util
 */
class Thread
{
    private $lockThreadOlderThan;

    public function __construct($lockThreadOlderThan) {
        $this->lockThreadOlderThan = $lockThreadOlderThan;
    }

    public function isAutolock($thread)
    {
        if ($this->lockThreadOlderThan) {
            $diff = $thread->getCdate()->diff(new \DateTime());
            if ($diff->days > $this->lockThreadOlderThan) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }
}