<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use DateTimeInterface;

#[ORM\Table(name: "workingforum_post")]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Post
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Thread", inversedBy: "post")]
    #[ORM\JoinColumn(name: "thread_id", referencedColumnName: "id", nullable: true)]
    private ?Thread $thread;

    #[ORM\Column(name: "content", type: "text")]
    #[Assert\NotBlank(message: "post.not_blank")]
    private string $content;

    #[ORM\Column(name: "published", type: "boolean")]
    private ?bool $published;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\UserInterface")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: true)]
    private UserInterface $user;

    #[ORM\Column(name: "cdate", type: "datetime")]
    #[Assert\NotBlank]
    private DateTimeInterface $cdate;

    #[ORM\Column(name: "ip", type: "string")]
    private string $ip; // FOR LEGAL AND SECURITY REASON

    #[ORM\Column(name: "moderateReason", type: "text", nullable: true)]
    private ?string $moderateReason;

    #[ORM\OneToMany(targetEntity: "Yosimitso\WorkingForumBundle\Entity\PostReport", mappedBy: "post", cascade: ["remove"])]
    private Collection $postReport;

    #[ORM\OneToMany(targetEntity: "Yosimitso\WorkingForumBundle\Entity\PostVote", mappedBy: "post", cascade: ["remove"])]
    private Collection $postVote;

    #[ORM\Column(name: "voteUp", type: "integer", nullable: true)]
    private ?int $voteUp;

    #[ORM\OneToMany(targetEntity: "Yosimitso\WorkingForumBundle\Entity\File", mappedBy: "post", cascade: ["persist", "remove"])]
    private Collection $files;

    private array $filesUploaded;

    private bool $addSubscription;

    public function __construct(UserInterface $user = null, Thread $thread = null)
    {
        $this->files = new ArrayCollection();
        $this->postReport = new ArrayCollection();
        $this->postVote = new ArrayCollection();
        $this->addSubscription = false;
        $this->setCdate(new DateTime)
            ->setPublished(1)
            ->setIp(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0);

        if (!is_null($user)) {
            $this->setUser($user);
        }

        if (!is_null($thread)) {
            $this->setThread($thread);
        }

        $this->moderateReason = null;
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

    public function setContent(string $content): self
    {
        $this->content = htmlentities(strip_tags($content));

        return $this;
    }

    public function getContent(): string
    {
        return html_entity_decode($this->content);
    }

    public function isPublished(): bool
    {
        return (bool) $this->published;
    }

    public function setPublished(?bool $published): self
    {
        $this->published = $published;

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

    public function setCdate(DateTime $cdate): self
    {
        $this->cdate = $cdate;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = htmlentities($ip);

        return $this;
    }

    public function getModerateReason(): ?string
    {
        return $this->moderateReason;
    }

    public function setModerateReason(?string $moderateReason): self
    {
        $this->moderateReason = $moderateReason;

        return $this;
    }

    public function getPostReport(): Collection
    {
        return $this->postReport;
    }
    
    public function addPostReport(PostReport $postReport): self
    {
        $this->postReport[] = $postReport;
        
        return $this;
    }
    
    public function removePostReport(int $index): self
    {
        unset($this->postReport[$index]);
        
        return $this;
    }

    public function getPostVote(): Collection
    {
        return $this->postVote;
    }

    public function addPostVote(PostVote $postVote): self
    {
        $this->postVote[] = $postVote;

        return $this;
    }

    public function removePostVote(int $index): self
    {
        unset($this->postVote[$index]);

        return $this;
    }

    public function getVoteUp(): int
    {
        return (int) $this->voteUp;
    }

    public function setVoteUp(?int $voteUp): self
    {
        $this->voteUp = $voteUp;
        
        return $this;
    }

    public function addVoteUp(): self
    {
        $this->voteUp += 1;
        return $this;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(array|File $files): self
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
    
    public function removeFile(int $index): self
    {
        unset($this->files[$index]);

        return $this;
    }

    public function setFilesUploaded(array $filesUploaded): self
    {
        $this->filesUploaded = $filesUploaded;

        return $this;
    }

    public function getFilesUploaded(): array
    {
        return $this->filesUploaded;
    }

    public function addFilesUploaded(File $file): self
    {
        $this->filesUploaded[] = $file;
        return $this;
    }

    public function getAddSubscription(): bool
    {
        return $this->addSubscription;
    }

    public function setAddSubscription(bool $addSubscription): self
    {
        $this->addSubscription = $addSubscription;

        return $this;
    }
}
