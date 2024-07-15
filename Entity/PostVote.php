<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use phpDocumentor\Reflection\DocBlock\Type\Collection;
use Yosimitso\WorkingForumBundle\Entity\Post as Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "workingforum_post_vote")]
#[ORM\Entity(repositoryClass: "Yosimitso\WorkingForumBundle\Repository\PostVoteRepository")]
class PostVote
{
    const VOTE_UP = 1;

    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Post", inversedBy: "postVote")]
    #[ORM\JoinColumn(name: "post_id", referencedColumnName: "id", nullable: false)]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Thread")]
    #[ORM\JoinColumn(name: "thread_id", referencedColumnName: "id", nullable: true)]
    private ?Thread $thread;

    #[ORM\Column(name: "voteType", type: "integer")]
    private int $voteType;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\UserInterface")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private UserInterface $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setThread(?Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setVoteType(int $voteType): self
    {
        $this->voteType = $voteType;

        return $this;
    }

    public function getVoteType(): int
    {
        return $this->voteType;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}

