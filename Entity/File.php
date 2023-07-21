<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeInterface;

#[ORM\Table(name: "workingforum_file")]
#[ORM\Entity]
class File
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\Column(name: "filename", type: "string", length: 100)]
    private string $filename;

    #[ORM\Column(name: "original_name", type: "string", length: 100)]
    private string $originalName;

    #[ORM\Column(name: "path", type: "string", length: 255, unique: true)]
    private string $path;

    #[ORM\Column(name: "extension", type: "string", length: 10)]
    private string $extension;

    #[ORM\Column(name: "size", type: "bigint")]
    private string $size;

    #[ORM\Column(name: "cdate", type: "datetime")]
    private DateTimeInterface $cdate;

    #[ORM\ManyToOne(targetEntity: "Yosimitso\WorkingForumBundle\Entity\Post", inversedBy: "files")]
    #[ORM\JoinColumn(name: "post_id", referencedColumnName: "id", nullable: true)]
    private Post $post;
    
    public function __construct() {
        $this->cdate = new DateTime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): string
    {
        return $this->size;
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

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}

