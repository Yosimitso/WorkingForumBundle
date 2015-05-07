<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Topic
 *
 * @ORM\Table(name="forum_topic")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\TopicRepository")
 */
class Topic
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
     *
    * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Subforum")
     *  @ORM\JoinColumn(name="subforum_id",referencedColumnName="id",nullable=true)
     * @var arrayCollection
     */
    private $subforum;
    
     /**
     * @var integer
     *
     * @ORM\Column(name="subforum_id", type="integer")
     */
    
    private $subforumId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="author_id", type="integer")
     */
    private $authorId;
    
     /**
    * @ORM\ManyToOne(targetEntity="Yosimitso\UserBundle\Entity\User")
     *  @ORM\JoinColumn(name="author_id",referencedColumnName="id",nullable=false)
     * @var arrayCollection
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cdate", type="datetime")
     */
    private $cdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbReplies", type="integer")
     */
    private $nbReplies;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastReplyDate", type="datetime")
     */
    private $lastReplyDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="resolved", type="boolean", nullable=true)
     */
    private $resolved;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean", nullable=true)
     */
    private $locked;

    /**
     * @var string 
     * @ORM\Column(name="label", type="string")
     */
    private $label;
            
    /**
     * @var string
     * @ORM\Column(name="sublabel", type="string")
     */
    
    private $subLabel;
    
    
     /**
     * @var string
     * @ORM\Column(name="slug", type="string", nullable=true)
     */
    
    private $slug;
    
    
    /**
   * @ORM\OneToMany(targetEntity="Yosimitso\WorkingForumBundle\Entity\Post", mappedBy="topic", cascade={"persist"})
   
     * @var arrayCollection
     *
     */
    private $post;
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
     * Set subforum
     *
     * @param integer $subforum
     * @return Topic
     */
    public function setSubforum(\Yosimitso\WorkingForumBundle\Entity\Subforum $subforum)
    {
        $this->subforum = $subforum;
    
        return $this;
    }

    /**
     * Get subforum
     *
     * @return \Yosimitso\WorkingForumBundle\Entity\Subforum
     */
    public function getSubforum()
    {
        return $this->subforum;
    }

    /**
     * Set authorId
     *
     * @param integer $authorId
     * @return Topic
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    
        return $this;
    }

    /**
     * Get authorId
     *
     * @return integer 
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * Set cdate
     *
     * @param \DateTime $cdate
     * @return Topic
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
     * Set nbReplies
     *
     * @param integer $nbReplies
     * @return Topic
     */
    public function setNbReplies($nbReplies)
    {
        $this->nbReplies = $nbReplies;
    
        return $this;
    }

    /**
     * Get nbReplies
     *
     * @return integer 
     */
    public function getNbReplies()
    {
        return $this->nbReplies;
    }
    
    public function addNbReplies($nb)
    {
        $this->nbReplies += $nb;
        return $this;
    }

    /**
     * Set lastReplyDate
     *
     * @param \DateTime $lastReplyDate
     * @return Topic
     */
    public function setLastReplyDate($lastReplyDate)
    {
        $this->lastReplyDate = $lastReplyDate;
    
        return $this;
    }

    /**
     * Get lastReplyDate
     *
     * @return \DateTime 
     */
    public function getLastReplyDate()
    {
        return $this->lastReplyDate;
    }

    /**
     * Set resolved
     *
     * @param boolean $resolved
     * @return Topic
     */
    public function setResolved($resolved)
    {
        $this->resolved = $resolved;
    
        return $this;
    }

    /**
     * Get resolved
     *
     * @return boolean 
     */
    public function getResolved()
    {
        return $this->resolved;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Topic
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    
        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }
    
     /**
     * Set subforumId
      *
     *
     * @param integer $subforumId
     * @return Topic
     */
    public function setSubforumId($subforumId)
    {
        $this->subforumId = $subforumId;
    
        return $this;
    }

    /**
     * Get subforumId
     *
     * @return integer 
     */
    public function getSubforumId()
    {
        return $this->subforumId;
    }
    
    /**
     * Set label
     *
     * @param string $label
     * @return Topic
     */
    public function setLabel($label)
    {
        $this->label = $label;
    
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    
    /**
     * Set SubLabel
     *
     * @param string $subLabel
     * @return Topic
     */
    public function setSublabel($subLabel)
    {
        $this->subLabel = $subLabel;
    
        return $this;
    }

    /**
     * Get subLabel
     *
     * @return string 
     */
    public function getSubLabel()
    {
        return $this->subLabel;
    }
    
     /**
     * Set author
     *
     * @param  $author
     * @return Topic
     */
    public function setAuthor(\Yosimitso\UserBundle\Entity\User $author)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \Yosimitso\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    
     /**
     * Set slug
     *
     * @param string $slug
     * @return Topic
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
    
    /**
     * Set post
     *
     * @param $post
     * @return Topic
     */
    public function setPost($post)
    {
        $this->post = $post;
    
        return $this;
    }

    /**
     * Get post
     *
     * @return \Yosimitso\WorkingForumBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }
    
    public function addPost(\Yosimitso\WorkingForumBundle\Entity\Post $post)
    {
        $this->post[] = $post;
        return $this;
    }

}
