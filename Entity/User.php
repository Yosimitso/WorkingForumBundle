<?php
namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\MappedSuperclass
 */
abstract class User implements \Yosimitso\WorkingForumBundle\Entity\UserInterface
{
            /**
     * @var integer
     */
	 protected $id;
       /**   
         * @var string
         */
      protected $username;
    
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
       * 
       * @var boolean
       *  @ORM\Column(name="banned", type="boolean", nullable=true)
       */   
         protected $banned;
         
 
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
            
            return $this;
        }
     public function setNbPost($nbPost)
	{
	$this->nbPost = $nbPost;

        return $this;
    }
    
    public function addNbPost($nbPost)
    {
        $this->nbPost += $nbPost;
        return $this;
    }
	
	public function getNbPost()
	{
	return $this->nbPost;
	}
        
    public function getBanned()
    {
        return $this->banned;
    }
    
    public function setBanned($banned)
    {
        $this->banned = $banned;
        
        return $this;
    }
}