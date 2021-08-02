<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Post
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 *
 * @ORM\Table(name="workingforum_post")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
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
     * @var Thread
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Thread", inversedBy="post")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id", nullable=true)
     */
    private $thread;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank(message="post.not_blank")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;


    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\UserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cdate", type="datetime")
     * @Assert\NotBlank()
     */
    private $cdate;

    /** var string
     *
     * @ORM\Column(name="ip", type="string")
     */
    private $ip; // FOR LEGAL AND SECURITY REASON

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Yosimitso\WorkingForumBundle\Entity\PostVote",
     *     mappedBy="post",
     *     cascade={"remove"}
     * )
     */
    private $postVote;

    /**
     * @var integer
     * @ORM\Column(name="voteUp", type="integer", nullable=true)
     */

    private $voteUp;

    /**
     * @ORM\OneToMany(targetEntity="Yosimitso\WorkingForumBundle\Entity\File", mappedBy="post", cascade={"persist","remove"})
     *
     * @var ArrayCollection
     */
    private $files;

    /**
     *
     */
    private $filesUploaded;

    /**
     * @var boolean
     */
    private $addSubscription;



    /**
     * Post constructor.
     * @param UserInterface|null $user
     * @param Thread|null $thread
     */
    public function __construct(UserInterface $user = null, Thread $thread = null)
    {
        $this->setCdate(new \DateTime)
            ->setPublished(1)
            ->setIp(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0);

        if (!is_null($user)) {
            $this->setUser($user);
        }

        if (!is_null($thread)) {
            $this->setThread($thread);
        }
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
     * @return Thread
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
        $this->ip = htmlentities($ip);

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
    
    public function addPostReport(PostReport $postReport)
    {
        $this->postReport[] = $postReport;
        
        return $this;
    }
    
    public function removePostReport($index)
    {
        unset($this->postReport[$index]);
        
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPostVote()
    {
        return $this->postVote;
    }

    public function addPostVote(PostVote $postVote)
    {
        $this->postVote[] = $postVote;

        return $this;
    }

    public function removePostVote($index)
    {
        unset($this->postVote[$index]);

        return $this;
    }


    /**
     * @return int
     */

    public function getVoteUp()
    {
        return $this->voteUp;
    }

    public function setVoteUp($voteUp)
    {
        $this->voteUp = $voteUp;
        
        return $this;
    }
    /**
     * @return Post
     */

    public function addVoteUp()
    {
        $this->voteUp += 1;
        return $this;
    }

    /**
 * @return ArrayCollection
 */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return Post
     */
    public function addFile($files)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->files[] = $file;
            }
        } else {
            $this->files[] = $files;
        }

        return $this;
    }
    
    public function removeFile($index)
    {
        unset($this->files[$index]);
    }

    /**
     * @param filesUploaded
     *
     * @return Post
     */
    public function setFilesUploaded($filesUploaded)
    {
        $this->filesUploaded = $filesUploaded;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFilesUploaded()
    {
        return $this->filesUploaded;
    }

    /**
     * @param $file
     * @return $this
     */
    public function addFilesUploaded($file)
    {
        $this->filesUploaded[] = $file;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAddSubscription()
    {
        return $this->addSubscription;
    }

    /**
     * @param boolean $addSubscription
     */
    public function setAddSubscription($addSubscription)
    {
        $this->addSubscription = $addSubscription;
    }



}
