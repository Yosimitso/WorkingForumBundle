<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subforum
 *
 * @ORM\Table(name="forum_subforum")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\SubforumRepository")
 */
class Subforum
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

  
    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    
    private $description;

    
    
     /**
      * @var arrayCollection
      * @ORM\JoinColumn(name="forum_id",referencedColumnName="id")
    * @ORM\ManyToOne(targetEntity="Yosimitso\WorkingForumBundle\Entity\Forum", inversedBy="subForum")
     
     */
    
    private $forum;
    
    // 
    
    /**
     * 
     * @var string
     * @ORM\Column(name="name", type="string")
     * 
     */
    private $name;
    
     /**
     * 
     * @var integer
     * @ORM\Column(name="nb_topic", type="integer", nullable=true)
     * 
     */
    
    
    private $nbTopic;
    
    
      /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;
    
    
     /**
     * 
     * @var integer
     * @ORM\Column(name="nb_post", type="integer",nullable=true)
     * 
     */
    private $nbPost;
    
    
     /**
     * 
     * @var datetime
     * @ORM\Column(name="last_reply_date", type="datetime",nullable=true)
     * 
     */
    private $lastReplyDate;
    
     public function getForum() {
        return $this->forum;
    }
    
    public function setForum($forum)
    {
        $this->forum = $forum;
    }
    
  
    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        if (empty($this->slug))
        {
            $this->slug = $this->clean($this->name);
        }
    }
    
  
    
     public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
     public function getNbTopic() {
        return $this->nbTopic;
    }
    
    public function setNbTopic($nb_topic)
    {
        $this->nbTopic = $nb_topic;
    }
    
     public function getNbPost() {
        return $this->nbPost;
    }
    
    public function setNbPost($nb_post)
    {
        $this->nbPost = $nb_post;
    }
    
     /**
     * Set slug
     *
     * @param string $slug
     * @return Forum
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
   
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
      public function getLastReplyDate() {
        return $this->lastReplyDate;
    }
    
    public function setLastReplyDate($lastReplyDate)
    {
        $this->lastReplyDate = $lastReplyDate;
    }

   public  function clean ($str)
{
	/** Mise en minuscules (chaîne utf-8 !) */
	$str = mb_strtolower($str, 'utf-8');
	/** Nettoyage des caractères */
	mb_regex_encoding('utf-8');
	$str = trim(preg_replace('/ +/', ' ', mb_ereg_replace('[^a-zA-Z\p{L}]+', ' ', $str)));
	/** strtr() sait gérer le multibyte */
	$str = strtr($str, array(
	' ' => '-', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'a'=>'a', 'a'=>'a', 'a'=>'a', 'ç'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'd'=>'d', 'd'=>'d', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'g'=>'g', 'g'=>'g', 'g'=>'g', 'h'=>'h', 'h'=>'h', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', '?'=>'i', 'j'=>'j', 'k'=>'k', '?'=>'k', 'l'=>'l', 'l'=>'l', 'l'=>'l', '?'=>'l', 'l'=>'l', 'ñ'=>'n', 'n'=>'n', 'n'=>'n', 'n'=>'n', '?'=>'n', '?'=>'n', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'o'=>'o', 'o'=>'o', 'o'=>'o', 'œ'=>'o', 'ø'=>'o', 'r'=>'r', 'r'=>'r', 's'=>'s', 's'=>'s', 's'=>'s', 'š'=>'s', '?'=>'s', 't'=>'t', 't'=>'t', 't'=>'t', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'w'=>'w', 'ý'=>'y', 'ÿ'=>'y', 'y'=>'y', 'z'=>'z', 'z'=>'z', 'ž'=>'z'
	));
	return $str;
}
}
