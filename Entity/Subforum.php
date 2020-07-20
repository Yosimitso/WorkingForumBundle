<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Yosimitso\WorkingForumBundle\Util\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Subforum
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Repository\SubforumRepository")
 * @ORM\Table(name="workingforum_subforum")
 */
class Subforum
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
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var Forum
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Forum", inversedBy="subForum")
     * @ORM\JoinColumn(name="forum_id", referencedColumnName="id")
     */
    private $forum;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_thread", type="integer", nullable=true)
     */
    private $nbThread;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_post", type="integer", nullable=true)
     */
    private $nbPost;

    /**
     * @var \Datetime
     * @ORM\Column(name="last_reply_date", type="datetime", nullable=true)
     *
     */
    private $lastReplyDate;

    /**
     * @var UserInterface|null
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\UserInterface")
     * @ORM\JoinColumn(name="lastReplyUser", referencedColumnName="id", nullable=true)
     */
    private $lastReplyUser;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Yosimitso\WorkingForumBundle\Entity\Thread",
     *     mappedBy="subforum",
     *     cascade={"remove"}
     * )
     */
    private $thread;

    /** @var ArrayCollection
     * @ORM\Column(name="allowed_roles",type="array", nullable=true)
     */

    private $allowedRoles;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->allowedRoles = new ArrayCollection;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Subforum
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Forum
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * @param Forum $forum
     *
     * @return Subforum
     */
    public function setForum(Forum $forum)
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        if (empty($this->slug)) {
            $this->slug = Slugify::convert($this->name);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNbThread()
    {
        return $this->nbThread;
    }

    /**
     * @param mixed $nbThread
     *
     * @return Subforum
     */
    public function setNbThread($nbThread)
    {
        $this->nbThread = $nbThread;

        return $this;
    }

    public function addNbThread($nb)
    {
        $this->nbThread += $nb;

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
     * @param string $slug
     *
     * @return Subforum
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * @param mixed $nbPost
     *
     * @return Subforum
     */
    public function setNbPost($nbPost)
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    public function addNbPost($nb)
    {
        $this->nbPost += $nb;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastReplyDate()
    {
        return $this->lastReplyDate;
    }

    /**
     * @param mixed $lastReplyDate
     *
     * @return Subforum
     */
    public function setLastReplyDate($lastReplyDate)
    {
        $this->lastReplyDate = $lastReplyDate;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getLastReplyUser()
    {
        return $this->lastReplyUser;
    }

    /**
     * @param UserInterface|null $lastReplyUser
     *
     * @return Subforum
     */
    public function setLastReplyUser(?UserInterface $lastReplyUser)
    {
        $this->lastReplyUser = $lastReplyUser;

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
     * @param ArrayCollection $thread
     *
     * @return Subforum
     */
    public function setThread(ArrayCollection $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * @param Thread $thread
     *
     * @return $this
     */
    public function addThread(Thread $thread)
    {
        $this->thread[] = $thread;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAllowedRoles()
    {
        return $this->allowedRoles;
    }

    /**
     * @param array $allowedRoles
     *
     * @return Subforum
     */
    public function setAllowedRoles(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasAllowedRoles()
    {
        // Check if there is one or more allowed role and is not an empty one
        if (!is_null($this->allowedRoles) && count($this->allowedRoles) >= 1 && !empty($this->allowedRoles[0])) {
            return true;
        }

        return false;
    }

    /**
     * @param UserInterface $user
     * @return bool
     * Update statistic on new post
     */
    public function newPost(UserInterface $user)
    {
        $this->setNbPost($this->getNbPost() + 1)
            ->setLastReplyDate(new \DateTime)
            ->setLastReplyUser($user);

        return true;
    }

    /**
     * @param UserInterface $user
     * @return bool
     * Update statistic on new post
     */
    public function newThread(UserInterface $user)
    {
        $this->setNbPost($this->getNbPost() + 1)
            ->setNbThread($this->getNbThread() + 1)
            ->setLastReplyDate(new \DateTime)
            ->setLastReplyUser($user);

        return true;
    }
}
