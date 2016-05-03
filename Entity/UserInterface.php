<?php
namespace Yosimitso\WorkingForumBundle\Entity;


interface UserInterface
{
 
    public function getId();
    public function getUsername();
    public function getAvatarUrl();
    public function getNbPost();
     public function setAvatarUrl($avatar_url);
     public function setNbPost($nbPost);
     public function addNbPost($nb);
     
}
