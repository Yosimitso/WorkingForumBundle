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
    * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Topic", inversedBy="post")
     *  @ORM\JoinColumn(name="topic_id",referencedColumnName="id",nullable=true)
     * @var arrayCollection
     */
    private $topic;
    
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

    
    public function getUser() {
        return $this->user;
    }
    
    public function setUser(\Yosimitso\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }
    
     public function getTopic() {
        return $this->topic;
    }
    
    public function setTopic(\Yosimitso\WorkingForumBundle\Entity\Topic $topic)
    {
        $this->topic = $topic;
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
        $this->content = $content;
    
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
}
