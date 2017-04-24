<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Subforum
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Subforum", inversedBy="thread")
     * @ORM\JoinColumn(name="subforum_id", referencedColumnName="id", nullable=false)
     */
    private $subforum;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
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
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\User")
     * @ORM\JoinColumn(name="lastReplyUser", referencedColumnName="id", nullable=true)
     */
    private $lastReplyUser;

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
     *
     * @todo use translated error messages
     *
     * @ORM\Column(name="label", type="string")
     * @Assert\NotBlank(message="Vous devez entrez un titre")
     * @Assert\Length(
     *     min=5,
     *     minMessage="Votre titre doit contenir au moins {{ limit }} caracteres",
     *     max=50,
     *     maxMessage="Votre titre ne doit pas contenir plus de {{ limit }} caracteres"
     * )
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="sublabel", type="string")
     */
    private $subLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="Yosimitso\WorkingForumBundle\Entity\Post", mappedBy="thread", cascade={"persist","remove"})
     *
     * @var ArrayCollection
     */
    private $post;

    /**
     * @var boolean
     * @ORM\Column(name="pin", type="boolean", nullable=true)
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

    public function __construct()
    {
        $this->post = new ArrayCollection;
    }

    /**
     * @param Subforum $subforum
     *
     * @return Thread
     */
    public function setSubforum(Subforum $subforum)
    {
        $this->subforum = $subforum;

        return $this;
    }

    /**
     * @return Subforum
     */
    public function getSubforum()
    {
        return $this->subforum;
    }

    /**
     * @param \DateTime $cdate
     *
     * @return Thread
     */
    public function setCdate($cdate)
    {
        $this->cdate = $cdate;

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
     * @param integer $nbReplies
     *
     * @return Thread
     */
    public function setNbReplies($nbReplies)
    {
        $this->nbReplies = $nbReplies;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNbReplies()
    {
        return $this->nbReplies;
    }

    /**
     * @param $nb
     *
     * @return $this
     */
    public function addNbReplies($nb)
    {
        $this->nbReplies += $nb;

        return $this;
    }

    /**
     * @param \DateTime $lastReplyDate
     *
     * @return Thread
     */
    public function setLastReplyDate($lastReplyDate)
    {
        $this->lastReplyDate = $lastReplyDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastReplyDate()
    {
        return $this->lastReplyDate;
    }

    /**
     * @return UserInterface
     */
    public function getLastReplyUser()
    {
        return $this->lastReplyUser;
    }

    /**
     * @param UserInterface $lastReplyUser
     *
     * @return Thread
     */
    public function setLastReplyUser(UserInterface $lastReplyUser)
    {
        $this->lastReplyUser = $lastReplyUser;

        return $this;
    }

    /**
     * @param boolean $resolved
     *
     * @return Thread
     */
    public function setResolved($resolved)
    {
        $this->resolved = $resolved;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getResolved()
    {
        return $this->resolved;
    }

    /**
     * @param boolean $locked
     *
     * @return Thread
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * @param string $label
     *
     * @return Thread
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $subLabel
     *
     * @return Thread
     */
    public function setSublabel($subLabel)
    {
        $this->subLabel = $subLabel;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubLabel()
    {
        return $this->subLabel;
    }

    /**
     * @return UserInterface
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     *
     * @return Thread
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param string $slug
     *
     * @return Thread
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param $post
     *
     * @return Thread
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function addPost(Post $post)
    {
        $this->post->add($post);

        return $this;
    }

    /**
     * @param boolean $pin
     *
     * @return Thread
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getPin()
    {
        return $this->pin;
    }

}
