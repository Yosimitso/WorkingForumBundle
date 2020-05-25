<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 *
 * @ORM\MappedSuperclass
 *
 */
 abstract class User implements UserInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Assert\NotBlank()
     */
    protected $id;

    /**
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     * @var string
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_url", type="string", nullable=true)
     */
    protected $avatarUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_post", type="integer", nullable=true)
     */
    protected $nbPost;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="banned", type="boolean", nullable=true)
     */
    protected $banned;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastReplyDate", type="datetime", nullable=true)
     */
    private $lastReplyDate;

    /**
     * @var string
     * @ORM\Column(name="email_address", type="string",nullable=true)
     */

    protected $emailAddress;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    /**
     * @param string|null $avatarUrl
     *
     * @return User
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * @param int $nbPost
     *
     * @return User
     */
    public function setNbPost($nbPost)
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    /**
     * @param int $nbPost
     *
     * @return User
     */
    public function addNbPost($nbPost)
    {
        $this->nbPost += $nbPost;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBanned()
    {
        return $this->banned;
    }

    /**
     * @param bool $banned
     *
     * @return User
     */
    public function setBanned($banned)
    {
        $this->banned = $banned;

        return $this;
    }

    /**
     * @param \DateTime $lastReplyDate
     *
     * @return Thread
     */
    public function setLastReplyDate($lastReplyDate)
    {
        $this->lastReplyDate = $lastReplyDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastReplyDate()
    {
        return $this->lastReplyDate;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }
}
