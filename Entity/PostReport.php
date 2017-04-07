<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PostReport
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 *
 * @ORM\Table(name="workingforum_post_report")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\PostReportRepository")
 */
class PostReport
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Post", inversedBy="postReport")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
     */
    private $post;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cdate", type="datetime")
     */
    private $cdate;
    
    /**
     * @var boolean
     *  @ORM\Column(name="processed", type="boolean", nullable=true)
     */  
    private $processed;

    /**
     * PostReport constructor.
     */
    public function __construct()
    {
        $this->cdate = new \DateTime;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param int $post
     *
     * @return PostReport
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     *
     * @return PostReport
     */
    public function setUser($user)
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
     * @return PostReport
     */
    public function setCdate(\DateTime $cdate)
    {
        $this->cdate = $cdate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isProcessed()
    {
        return (bool) $this->processed;
    }

    /**
     * @param bool $processed
     *
     * @return PostReport
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }
}
