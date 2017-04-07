<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Post
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 *
 * @ORM\Table(name="workingforum_post")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\PostRepository")
 */
class Post
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
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cdate", type="datetime")
     */
    private $cdate;

    /** var string
     *
     * @ORM\Column(name="ip", type="string")
     */
    private $ip; // FOR SECURITY REASON

    /**
     * @var string
     *
     * @ORM\Column(name="moderateReason", type="text",nullable=true)
     */
    private $moderateReason;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Yosimitso\WorkingForumBundle\Entity\PostReport",
     *     mappedBy="post",
     *     cascade={"remove"}
     * )
     */

    private $postReport;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->ip = htmlentities(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0);
    }

    /**
     * @return integer
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
     * @param string $content
     *
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = htmlentities(strip_tags($content));

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return html_entity_decode($this->content);
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param bool $published
     *
     * @return Post
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return Post
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
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

    /**
     * @return \DateTime
     */
    public function getCdate()
    {
        return $this->cdate;
    }

    /**
     * @param \DateTime $cdate
     *
     * @return Post
     */
    public function setCdate(\DateTime $cdate)
    {
        $this->cdate = $cdate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     *
     * @return Post
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string
     */
    public function getModerateReason()
    {
        return $this->moderateReason;
    }

    /**
     * @param string $moderateReason
     *
     * @return Post
     */
    public function setModerateReason($moderateReason)
    {
        $this->moderateReason = $moderateReason;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPostReport()
    {
        return $this->postReport;
    }
}
