<?php

namespace Yosimitso\WorkingForumBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use \Symfony\Component\Security\Core\User\UserInterface;
#[ORM\Table(name: "users")]
#[ORM\Entity]
class UserTest extends \Yosimitso\WorkingForumBundle\Entity\User implements UserInterface, EquatableInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    protected $id;


    #[ORM\Column(name: "username", type: "string", length: 255, unique: true)]
    protected $username;

    #[ORM\Column(name: "password", type: "string", length: 255)]
    protected $password;

    #[ORM\Column(name: "salt", type: "string", length: 255)]
    protected string $salt;

    #[ORM\Column(name: "roles", type: "array")]
    protected $roles = array();

    #[ORM\Column(name: "avatar_url", type: "string", nullable: true)]
    protected $avatarUrl;

    #[ORM\Column(name: "nb_post", type: "integer", nullable: true)]
    protected $nbPost;




    public function eraseCredentials(): void
    {
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }



    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
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

    public function isEqualTo(UserInterface $user): bool
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

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }
}
