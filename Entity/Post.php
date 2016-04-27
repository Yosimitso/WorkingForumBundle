<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="workingforum_post")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\PostRepository")
 */
class Post
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
    * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Thread", inversedBy="post")
     *  @ORM\JoinColumn(name="thread_id",referencedColumnName="id",nullable=true)
     * @var arrayCollection
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
    * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\User")
     *  @ORM\JoinColumn(name="user_id",referencedColumnName="id",nullable=true)
     * @var arrayCollection
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
    
    public function __construct()
    {
        $this->ip = htmlentities($_SERVER["REMOTE_ADDR"]);
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
    }
    
     public function getThread() {
        return $this->thread;
    }
    
    public function setThread(\Yosimitso\WorkingForumBundle\Entity\Thread $thread)
    {
        $this->thread = $thread;
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
     * Set content 
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content =strip_tags($content,'<br /><br><p><pre><ul><ol><li><strong><em><h1><h2><h3><h4><h5><h6><code><blockquote><a><img>');
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Post
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Post
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set cdate
     *
     * @param \DateTime $cdate
     * @return Post
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
     * Set cdate
     *
     * @param string $ip
     * @return Post
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
    
     /**
     * Set moderateReason 
     *
     * @param string $moderateReason 
     * @return Post
     */
    public function setModerateReason ($moderateReason )
    {
        $this->moderateReason  =$moderateReason ;
    
        return $this;
    }

    /**
     * Get moderateReason 
     *
     * @return string 
     */
    public function getModerateReason ()
    {
        return $this->moderateReason;
    }

}
