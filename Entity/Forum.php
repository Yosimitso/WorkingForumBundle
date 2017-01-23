<?php

namespace Yosimitso\WorkingForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Yosimitso\WorkingForumBundle\Util\Slugify;

/**
 * Class Forum
 *
 * @package Yosimitso\WorkingForumBundle\Entity
 *
 * @ORM\Table(name="workingforum_forum")
 * @ORM\Entity(repositoryClass="Yosimitso\WorkingForumBundle\Entity\ForumRepository")
 */
class Forum
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Yosimitso\WorkingForumBundle\Entity\Subforum",
     *     mappedBy="forum",
     *     cascade={"persist","remove"},
     *     orphanRemoval=true
     * )
     */
    private $subForum;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Forum
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubforum()
    {
        return $this->subForum;
    }

    /**
     * @param Subforum $subforum
     *
     * @return Forum
     */
    public function addSubForum(Subforum $subforum)
    {
        $this->subForum[] = $subforum;

        return $this;
    }

    /**
     * @param Subforum $subForum
     *
     * @return Forum
     */
    public function removeSubForum(Subforum $subForum)
    {
        $this->subForum->remove($subForum);
        $subForum->setForum(null);

        return $this;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
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

    /**
     * @param $name
     *
     * @return mixed|string
     */
    public function generateSlug($name)
    {
        $this->slug = Slugify::convert($name);

        return $this->slug;
    }

}
