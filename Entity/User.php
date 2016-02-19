<?php
namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\MappedSuperclass
 */
abstract class User implements \Yosimitso\WorkingForumBundle\Entity\UserInterface
{
    
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
    
    public function addNbPost($nb)
    {
        $this->nbPost += 1;
        return $this;
    }
	
	public function getNbPost()
	{
	return $this->nbPost;
	}
}