<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeInterface;

#[ORM\Table(name: "workingforum_post_report")]
#[ORM\Entity]
class PostReport
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Post", inversedBy: "postReport")]
    #[ORM\JoinColumn(name: "post_id", referencedColumnName: "id", nullable: false)]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\UserInterface")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private UserInterface $user;

    #[ORM\Column(name: "cdate", type: "datetime")]
    private DateTimeInterface $cdate;

    #[ORM\Column(name: "processed", type: "boolean", nullable: true)]
    private ?bool $processed;

    public function __construct()
    {
        $this->cdate = new DateTime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCdate(): DateTimeInterface
    {
        return $this->cdate;
    }

    public function setCdate(DateTimeInterface $cdate): self
    {
        $this->cdate = $cdate;

        return $this;
    }

    public function isProcessed(): bool
    {
        return (bool) $this->processed;
    }

    public function setProcessed(?bool $processed): self
    {
        $this->processed = $processed;

        return $this;
    }
}
