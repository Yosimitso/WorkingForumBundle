<?php

namespace Yosimitso\WorkingForumBundle\Entity;

/**
 * Interface UserInterface
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 */
interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function getId();

    public function getUsername();

    public function setUsername(string $username);

    public function getAvatarUrl();

    public function getNbPost();

    public function setAvatarUrl($avatar_url);

    public function setNbPost($nbPost);

    public function addNbPost($nb);
    
    public function getEmailAddress();
}
