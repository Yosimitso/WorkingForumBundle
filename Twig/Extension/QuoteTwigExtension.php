<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Util\Censorship as CensorshipUtil;

/**
 * Class QuoteTwigExtension
 *
 * @package Yosimitso\WorkingForumBundle\Twig\Extension
 */
class QuoteTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CensorshipUtil
     */
    private $censorshipUtil;

    /**
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        CensorshipUtil $censorship
    )
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->censorshipUtil = $censorship;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'quote',
                [$this, 'quote']
            ),
        ];
    }

    /**
     * @param $text
     *
     * @return mixed
     */
    public function quote($text)
    {
        $content = preg_replace_callback('#\[quote=([0-9]+)\]#',
            function ($listQuote) {

                /** @var Post $post */
                $post = $this->entityManager
                    ->getRepository('YosimitsoWorkingForumBundle:Post')
                    ->findOneById((int) $listQuote[1])
                ;

                if (!is_null($post) && empty($post->getModerateReason())) {
                    return "\n>**"
                        . $post->getUser()->getUsername()
                        . ' '
                        . $this->translator->trans('forum.has_written', [], 'YosimitsoWorkingForumBundle')
                        . " :** \n"
                        . '>'.$this->markdownQuote($this->quote($this->censorshipUtil->censor($post->getContent())))
                        . "\n";
                }

                return '';
            },
            $text
        );

        return $content;
    }

    private function markdownQuote($text) {
        return preg_replace('/\n/', "\n >", $text );
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'quote';
    }
}