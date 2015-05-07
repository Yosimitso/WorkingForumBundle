<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subforum
 *
 * @ORM\Table(name="forum_subforum")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\SubforumRepository")
 */
class Subforum
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
     * @var string
     * @ORM\Column(name="description", type="string", length=255)
     */
    
    private $description;

      /**
     * @var integer
     *
     * @ORM\Column(name="forum_id", type="integer")
     */
    private $forumId;
    
     /**
    * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Forum", inversedBy="subForum")
     *  @ORM\JoinColumn(name="forum_id",referencedColumnName="id",nullable=true)
     * @var arrayCollection
     */
    
    private $forum;
    
    
    /**
     * 
     * @var string
     * @ORM\Column(name="name", type="string")
     * 
     */
    private $name;
    
     /**
     * 
     * @var string
     * @ORM\Column(name="nb_topic", type="string")
     * 
     */
    
    
    private $nbTopic;
    
    
      /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;
    
    
     /**
     * 
     * @var string
     * @ORM\Column(name="nb_post", type="string")
     * 
     */
    private $nbPost;
    
    
     /**
     * 
     * @var datetime
     * @ORM\Column(name="last_reply_date", type="datetime")
     * 
     */
    private $lastReplyDate;
    
     public function getForum() {
        return $this->forum;
    }
    
    public function setUser(\Yosimitso\WorkingForumBundle\Entity\Forum $forum)
    {
        $this->forum = $forum;
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
    
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getForumId() {
        return $this->forumId;
    }
    
    public function setForumId($forumId)
    {
        $this->forumId = $forumId;
    }
    
     public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
     public function getNbTopic() {
        return $this->nbTopic;
    }
    
    public function setNbTopic($nb_topic)
    {
        $this->nbTopic = $nb_topic;
    }
    
     public function getNbPost() {
        return $this->nbPost;
    }
    
    public function setNbPost($nb_post)
    {
        $this->nbPost = $nb_post;
    }
    
     /**
     * Set slug
     *
     * @param string $slug
     * @return Forum
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
   
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
      public function getLastReplyDate() {
        return $this->lastReplyDate;
    }
    
    public function setLastReplyDate($lastReplyDate)
    {
        $this->lastReplyDate = $lastReplyDate;
    }

  
}
