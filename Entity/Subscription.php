<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "workingforum_subscription")]
class Subscription
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Thread", inversedBy: "subscriptions")]
    #[ORM\JoinColumn(name: "thread_id", referencedColumnName: "id", nullable: true)]
    private ?Thread $thread;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\UserInterface")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: true)]
    private ?UserInterface $user;

    public function __construct(Thread $thread, UserInterface $user)
    {
        $this->setThread($thread);
        $this->setUser($user);
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function setThread(?Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }


    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
}
