<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "workingforum_rules")]
#[ORM\Entity(repositoryClass: "Yosimitso\WorkingForumBundle\Repository\RulesRepository")]
class Rules
{
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private int $id;

    #[ORM\Column(name: "lang", type: "string", length: 50)]
    private string $lang;

    #[ORM\Column(name: "content", type: "text")]
    private string $content;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
