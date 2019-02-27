<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Util\Censorship as CensorshipUtil;

/**
 * Class CensorshipTwigExtension
 * @package Yosimitso\WorkingForumBundle\Twig\Extension
 */
class CensorshipTwigExtension extends \Twig_Extension
{
    /**
     * @var CensorshipUtil
     */
    private $censorshipUtil;

    public function __construct(
        CensorshipUtil $censorshipUtil
    )
    {
        $this->censorshipUtil = $censorshipUtil;
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
        return $this->censorshipUtil->censor($text);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'censorship';
    }
}