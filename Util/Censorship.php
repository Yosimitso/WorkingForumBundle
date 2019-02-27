<?php

namespace Yosimitso\WorkingForumBundle\Util;

use Doctrine\ORM\EntityManagerInterface;
use Yosimitso\WorkingForumBundle\Entity\File;

/**
 * Class FileUploader
 * @package Yosimitso\WorkingForumBundle\Util
 * Handle file upload system
 */
class Censorship
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    protected $paramCensorship;

    public function __construct(EntityManagerInterface $em, array $paramCensorship)
    {
        $this->em = $em;
        $this->paramCensorship = $paramCensorship;
    }

    /**
     * @param $text
     *
     * @return mixed
     */
    public function censor($text)
    {
        if (!$this->paramCensorship['enable']) {
            return $text;
        }

        $words = $this->em
            ->getRepository('YosimitsoWorkingForumBundle:Censorship')->getCensoredPatterns();

        if (is_null($words)) {
            return $text;
        }


        return preg_replace($words, $this->paramCensorship['replacement'], $text);
    }
}