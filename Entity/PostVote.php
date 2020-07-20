<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Yosimitso\WorkingForumBundle\Entity\Post as Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PostVote
 *
 * @ORM\Table(name="workingforum_post_vote")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Repository\PostVoteRepository")
 */
class PostVote
{
    const VOTE_UP = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Post", inversedBy="postVote")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
     */
    private $post;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Thread")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id", nullable=true)
     */
    private $thread;

    /**
     * @var int
     *
     * @ORM\Column(name="voteType", type="integer")
     */
    private $voteType;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\UserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set post
     *
     * @param Post $post
     *
     * @return PostVote
     */
    public function setPost(Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
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
     * @return ArrayCollection
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set voteType
     *
     * @param integer $voteType
     *
     * @return PostVote
     */
    public function setVoteType($voteType)
    {
        $this->voteType = $voteType;

        return $this;
    }

    /**
     * Get voteType
     *
     * @return int
     */
    public function getVoteType()
    {
        return $this->voteType;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     *
     * @return PostVote
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}

