<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use \Symfony\Component\Security\Core\User\UserInterface;

/**
 * Used by functionnal tests
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class UserTest extends \Yosimitso\WorkingForumBundle\Entity\User implements UserInterface, EquatableInterface
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(name="email_address", type="string",nullable=true)
     */

    protected $emailAddress;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    public $roles = array();

    /**
     * @var string
     * @ORM\Column(name="avatar_url", type="string",nullable=true)
     */
    protected $avatarUrl;

    /**
     * @var integer
     * @ORM\Column(name="nb_post", type="integer",nullable=true)
     */
    protected $nbPost;

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }



    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole($role)
    {
        $this->roles[] = $role;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl($avatar_url)
    {
        $this->avatarUrl = $avatar_url;
    }



    public function setNbPost($nbPost)
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    public function getNbPost()
    {
        return $this->nbPost;
    }

    public function addNbPost($nbpost)
    {
        $this->nbPost += $nbpost;
        return $this;
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->password,
            $this->emailAddress,
            $this->username,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->password,
            $this->emailAddress,
            $this->username,
            ) = unserialize($serialized, array('allowed_classes' => false));
    }
}
