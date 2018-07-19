<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="workingforum_rules")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Repository\RulesRepository")
 */
class Rules
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $lang;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    public function getId()
    {
        return $this->id;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang(string $lang)
    {
        $this->lang = $lang;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }
}
