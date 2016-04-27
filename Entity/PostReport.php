<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostReport
 *
 * @ORM\Table(name="workingforum_post_report")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\PostReportRepository")
 */
class PostReport
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Post")
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
     *  @ORM\Column(name="processed", type="boolean",nullable=true)
     */  
    private $processed;
    
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
     * Set post
     *
     * @param \Yosimitso\WorkingForumBundle\Entity\Post $post
     * @return PostReport
     */
    public function setPost(\Yosimitso\WorkingForumBundle\Entity\Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return integer 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param \Yosimitso\WorkingForumBundle\Entity\User $user
     * @return PostReport
     */
    public function setUser(\Yosimitso\WorkingForumBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set cdate
     *
     * @param \DateTime $cdate
     * @return PostReport
     */
    public function setCdate($cdate)
    {
        $this->cdate = $cdate;

        return $this;
    }

    /**
     * Get cdate
     *
     * @return \DateTime 
     */
    public function getCdate()
    {
        return $this->cdate;
    }
    
     /**
     * Set processed
     *
     * @param boolean $processed
     * @return PostReport
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get processed
     *
     * @return boolean
     */
    public function getProcessed()
    {
        return $this->processed;
    }
}
