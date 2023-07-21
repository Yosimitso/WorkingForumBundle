<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Yosimitso\WorkingForumBundle\Util\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "workingforum_forum")]
#[ORM\Entity]
class Forum
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\Column(name: "name", type: "string", length: 255)]
    #[Assert\NotBlank(message: "forum.not_blank")]
    private string $name;

    #[ORM\Column(name: "slug", type: "string", length: 255)]
    private string $slug;

    #[ORM\OneToMany(mappedBy: "forum", targetEntity: "Yosimitso\WorkingForumBundle\Entity\Subforum", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $subForum;

    public function __construct()
    {
        $this->subForum = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): Forum
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubforum(): Collection
    {
        return $this->subForum;
    }

    public function addSubForum(Subforum $subforum): self
    {
        $this->subForum[] = $subforum;

        return $this;
    }

    public function removeSubForum(int $index): self
    {
        $this->subForum->remove($index);

        return $this;
    }

    public function setSubForum(array $subforums): self
    {
        $this->subForum = $subforums;

        return $this;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function generateSlug(string $name): string
    {
        $this->slug = Slugify::convert($name);

        return $this->slug;
    }
}
