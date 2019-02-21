<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Forum
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 *
 * @ORM\Table(name="workingforum_censorship")
 * @ORM\Entity()
 */
class Censorship
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pattern", type="string", length=255)
     * @Assert\NotBlank(message="forum.not_blank")
     */
    private $pattern;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $pattern
     *
     * @return Censorship
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

}

