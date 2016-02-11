<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Thread
 *
 * @ORM\Table(name="workingforum_thread")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\ThreadRepository")
 */
class Thread
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
     *  @ORM\JoinColumn(name="subforum_id",referencedColumnName="id",nullable=false)
     * @var arrayCollection
     */
    private $subforum;
    
 
  
    
     /**
    * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\User")
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
     * @Assert\NotBlank(message="Vous devez entrez un titre")
     * @Assert\Length(min=5, minMessage="Votre titre doit contenir au moins {{ limit }} caracteres", max=50, maxMessage="Votre titre ne doit pas contenir plus de {{ limit }} caracteres")
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
   * @ORM\OneToMany(targetEntity="Yosimitso\WorkingForumBundle\Entity\Post", mappedBy="thread", cascade={"persist"})
   
     * @var arrayCollection
     *
     */
    private $post;
  
   /**
    * @var boolean
    *  @ORM\Column(name="pin", type="boolean", nullable=true)
    */
    private $pin;
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
     * @return Thread
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
     * Set cdate
     *
     * @param \DateTime $cdate
     * @return Thread
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
     * @return Thread
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
     * @return Thread
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
     * @return Thread
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
     * @return Thread
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
     * Set label
     *
     * @param string $label
     * @return Thread
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
     * @return Thead
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
     * @return Thread
     */
    public function setAuthor($author)
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
     * @return Thread
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
     * @return Thread
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
    
      /**
     * Set pin
     *
     * @param boolean $pin
     * @return Thread
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
    
        return $this;
    }

    /**
     * Get pin
     *
     * @return boolean
     */
    public function getPin()
    {
        return $this->pin;
    }

}
