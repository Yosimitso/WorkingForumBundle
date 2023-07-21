<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Yosimitso\WorkingForumBundle\Util\Slugify;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: "Yosimitso\WorkingForumBundle\Repository\SubforumRepository")]
#[ORM\Table(name: "workingforum_subforum")]
class Subforum
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\Column(name: "description", type: "string", length: 255, nullable: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Forum", inversedBy: "subForum")]
    #[ORM\JoinColumn(name: "forum_id", referencedColumnName: "id")]
    private Forum $forum;

    #[ORM\Column(name: "name", type: "string")]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(name: "nb_thread", type: "integer", nullable: true)]
    private ?int $nbThread;

    #[ORM\Column(name: "slug", type: "string", length: 255)]
    #[Assert\NotBlank]
    private string $slug;

    #[ORM\Column(name: "nb_post", type: "integer", nullable: true)]
    private ?int $nbPost;

    #[ORM\Column(name: "last_reply_date", type: "datetime", nullable: true)]
    private ?DateTimeInterface $lastReplyDate;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\UserInterface")]
    #[ORM\JoinColumn(name: "lastReplyUser", referencedColumnName: "id", nullable: true)]
    private ?UserInterface $lastReplyUser;

    #[ORM\OneToMany(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Thread", mappedBy: "subforum", cascade: ["remove"])]
    private Collection $thread;

    #[ORM\Column(name: "allowed_roles", type: "array", nullable: true)]
    private ?array $allowedRoles;

    public function __construct()
    {
        $this->allowedRoles = [];
        $this->lastReplyUser = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getForum(): Forum
    {
        return $this->forum;
    }

    public function setForum(Forum $forum): self
    {
        $this->forum = $forum;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        if (empty($this->slug)) {
            $this->slug = Slugify::convert($this->name);
        }

        return $this;
    }

    public function getNbThread(): ?int
    {
        return $this->nbThread;
    }

    public function setNbThread(?int $nbThread): self
    {
        $this->nbThread = $nbThread;

        return $this;
    }

    public function addNbThread(int $nb): self
    {
        $this->nbThread += $nb;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getNbPost(): ?int
    {
        return $this->nbPost;
    }

    public function setNbPost(?int $nbPost): self
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    public function addNbPost(int $nb): self
    {
        $this->nbPost += $nb;

        return $this;
    }

    public function getLastReplyDate(): ?DateTimeInterface
    {
        return $this->lastReplyDate;
    }

    public function setLastReplyDate(?DateTimeInterface $lastReplyDate)
    {
        $this->lastReplyDate = $lastReplyDate;

        return $this;
    }

    public function getLastReplyUser(): ?UserInterface
    {
        return $this->lastReplyUser;
    }


    public function setLastReplyUser(?UserInterface $lastReplyUser): self
    {
        $this->lastReplyUser = $lastReplyUser;

        return $this;
    }

    public function getThread(): Collection
    {
        return $this->thread;
    }

    public function setThread(Collection $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function addThread(Thread $thread): self
    {
        $this->thread[] = $thread;

        return $this;
    }

    public function getAllowedRoles(): ?array
    {
        return $this->allowedRoles;
    }

    public function setAllowedRoles(?array $allowedRoles): self
    {
        $this->allowedRoles = $allowedRoles;

        return $this;
    }

    public function hasAllowedRoles(): bool
    {
        // Check if there is one or more allowed role and is not an empty one
        if (!is_null($this->allowedRoles) && !empty($this->allowedRoles[0]) && count($this->allowedRoles) >= 1) {
            return true;
        }

        return false;
    }

    public function newPost(UserInterface $user): bool
    {
        $this->setNbPost($this->getNbPost() + 1)
            ->setLastReplyDate(new DateTime)
            ->setLastReplyUser($user);

        return true;
    }

    /** Update statistic on new post */
    public function newThread(UserInterface $user): bool
    {
        $this->setNbPost($this->getNbPost() + 1)
            ->setNbThread($this->getNbThread() + 1)
            ->setLastReplyDate(new \DateTime)
            ->setLastReplyUser($user);

        return true;
    }
}
