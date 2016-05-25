<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forum
 *
 * @ORM\Table(name="workingforum_forum")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\ForumRepository")
 */
class Forum
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
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
     /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;
    
    
    /**
   * @ORM\OneToMany(targetEntity="Yosimitso\WorkingForumBundle\Entity\Subforum", mappedBy="forum", cascade={"persist","remove"}, orphanRemoval=true)
   
     * @var arrayCollection
     *
     */
    private $subForum;
    
    public function removeSubForum($subForum)
    {
        $this->subForum->remove($subForum);
        $subForum->setForum(null);
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

    /**
     * Set name
     *
     * @param string $name
     * @return Forum
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function getSubforum()
    {
        return $this->subForum;
    }
    
    public function addSubForum(\Yosimitso\WorkingForumBundle\Entity\Subforum $subforum)
    {
        $this->subForum[] = $subforum; 
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
    
}
