<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Yosimitso\WorkingForumBundle\Entity\Post;

/**
 * Class CensorshipTwigExtension
 * @package Yosimitso\WorkingForumBundle\Twig\Extension
 */
class CensorshipTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    protected $paramCensorship;

    public function __construct(
        EntityManagerInterface $entityManager,
        array $paramCensorship
    )
    {
        $this->entityManager = $entityManager;
        $this->paramCensorship = $paramCensorship;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'censor',
                [$this, 'censor']
            ),
        ];
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

        $words = $this->entityManager
                    ->getRepository('YosimitsoWorkingForumBundle:Censorship')->findAll();

        if (is_null($words)) {
            return $text;
        }

        return preg_replace($words, $this->paramCensorship['replacement'], $text);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'censorship';
    }
}