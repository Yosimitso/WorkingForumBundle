<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Yosimitso\WorkingForumBundle\Entity\Post;

/**
 * Class QuoteTwigExtension
 *
 * @package Yosimitso\WorkingForumBundle\Twig\Extension
 */
class QuoteTwigExtension extends AbstractExtension
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
            new TwigFilter(
                'quote',
                [$this, 'quote']
            ),
        ];
    }

    /**
     * @param string $text
     *
     * @return mixed
     */
    public function quote($text)
    {
        $content = preg_replace_callback('#\[quote=([0-9]+)\]#',
            function ($listQuote) {

                /** @var Post $post */
                $post = $this->entityManager
                    ->getRepository(Post::class)
                    ->findOneById((int) $listQuote[1])
                ;

                if (!is_null($post) && empty($post->getModerateReason())) {
                    return "\n>**"
                        . $post->getUser()->getUsername()
                        . ' '
                        . $this->translator->trans('forum.has_written', [], 'YosimitsoWorkingForumBundle')
                        . " :** \n"
                        . '>'.$this->markdownQuote($this->quote($post->getContent()))
                        . "\n\n";
                }

                return '';
            },
            $text
        );

        return $content;
    }

    /**
     * @param string $text
     * @return string|string[]|null
     */
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
