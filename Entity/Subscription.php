<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Subscription
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 *
 * @ORM\Table(name="workingforum_subscription")
 * @ORM\Entity()
 */
class Subscription
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Thread", inversedBy="post")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id", nullable=true)
     */
    private $thread;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\UserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    public function __construct(Thread $thread, UserInterface $user)
    {
        $this->setThread($thread);
        $this->setUser($user);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Thread $thread
     *
     * @return Post
     */
    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     *
     * @return Post
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

}
