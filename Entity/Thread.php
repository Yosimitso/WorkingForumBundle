<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: "Yosimitso\WorkingForumBundle\Repository\ThreadRepository")]
#[ORM\Table(name: "workingforum_thread")]
class Thread
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Subforum", inversedBy: "thread")]
    #[ORM\JoinColumn(name: "subforum_id", referencedColumnName: "id", nullable: false)]
    private Subforum $subforum;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\UserInterface")]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id", nullable: true)]
    private ?UserInterface $author;

    #[ORM\Column(name: "cdate", type: "datetime")]
    #[Assert\NotBlank]
    private DateTimeInterface $cdate;

    #[ORM\Column(name: "nbReplies", type: "integer")]
    private int $nbReplies;

    #[ORM\Column(name: "lastReplyDate", type: "datetime")]
    private DateTimeInterface $lastReplyDate;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\UserInterface", cascade: ["persist"])]
    #[ORM\JoinColumn(name: "lastReplyUser", referencedColumnName: "id", nullable: true)]
    private ?UserInterface $lastReplyUser;

    #[ORM\Column(name: "resolved", type: "boolean", nullable: true)]
    private ?bool $resolved;

    #[ORM\Column(name: "locked", type: "boolean", nullable: true)]
    private ?bool $locked;

    #[ORM\Column(name: "label", type: "string")]
    #[Assert\NotBlank(message: "thread.label.not_blank")]
    #[Assert\Length(min: 5, minMessage: "thread.label.min_length", max: 50, maxMessage: "thread.label.max_length")]
    private string $label;

    #[ORM\Column(name: "sublabel", type: "string")]
    private string $subLabel;

    #[ORM\Column(name: "slug", type: "string", nullable: true)]
    private ?string $slug;

    #[ORM\OneToMany(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Post", mappedBy: "thread", cascade: ["persist", "remove"])]
    private Collection $post;

    #[ORM\Column(name: "pin", type: "boolean", nullable: true)]
    private ?bool $pin;

    #[ORM\OneToMany(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Subscription", mappedBy: "thread", cascade: ["remove"])]
    private Collection $subscriptions;

    public function __construct(UserInterface $user = null, Subforum $subforum = null)
    {
        $this->locked = false;
        $this->post = new ArrayCollection;
        $this->subscriptions = new ArrayCollection;
        $this->setLastReplyDate(new DateTime)
            ->setCdate(new DateTime)
            ->setNbReplies(1) // A THREAD MUST HAVE AT LEAST 1 POST
            ;

        if (!is_null($user)) {
            $this->setLastReplyUser($user)
                ->setAuthor($user)
                ;
        }

        if (!is_null($subforum)) {
            $this->setSubforum($subforum)
            ;
        }

        $this->pin = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setSubforum(Subforum $subforum): self
    {
        $this->subforum = $subforum;

        return $this;
    }

    public function getSubforum(): Subforum
    {
        return $this->subforum;
    }

    public function setCdate(DateTimeInterface $cdate): self
    {
        $this->cdate = $cdate;

        return $this;
    }

    public function getCdate(): DateTimeInterface
    {
        return $this->cdate;
    }

    public function setNbReplies(int $nbReplies): self
    {
        $this->nbReplies = $nbReplies;

        return $this;
    }

    public function getNbReplies(): int
    {
        return $this->nbReplies;
    }

    public function addNbReplies(int $nb): self
    {
        $this->nbReplies += $nb;

        return $this;
    }

    public function setLastReplyDate(DateTimeInterface $lastReplyDate): self
    {
        $this->lastReplyDate = $lastReplyDate;

        return $this;
    }

    public function getLastReplyDate(): DateTimeInterface
    {
        return $this->lastReplyDate;
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

    public function setResolved(bool $resolved): Thread
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

    public function setLocked(?bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function getLocked(): bool
    {
        return (bool) $this->locked;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setSublabel(string $subLabel): self
    {
        $this->subLabel = $subLabel;

        return $this;
    }

    public function getSubLabel(): string
    {
        return $this->subLabel;
    }

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

//    public function setPost(Collection $post): self
//    {
//        $this->post = $post;
//
//        return $this;
//    }

    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(Post $post): self
    {
        $this->post->add($post);

        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->post->removeElement($post);

        return $this;
    }

    public function setPin(?bool $pin): self
    {
        $this->pin = $pin;

        return $this;
    }

    public function getPin(): bool
    {
        return (bool) $this->pin;
    }

    /** Update statistic on new post */
    public function addReply(UserInterface $user): bool
    {
        $this->addNbReplies(1)
            ->setLastReplyDate(new \DateTime)
            ->setLastReplyUser($user);

        return true;
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        $this->subscriptions->add($subscription);

        return $this;
    }
}
