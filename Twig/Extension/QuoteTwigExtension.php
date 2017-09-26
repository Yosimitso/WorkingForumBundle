<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Yosimitso\WorkingForumBundle\Entity\Post;

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
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    )
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
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
                    return '>**'
                        . $post->getUser()->getUsername()
                        . ' '
                        . $this->translator->trans('forum.has_written', [], 'YosimitsoWorkingForumBundle')
                        . " :** \n"
                        . '>'.$this->markdownQuote($this->quote($post->getContent()))
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