<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Yosimitso\WorkingForumBundle\Entity\Post;


class QuoteTwigExtension extends AbstractExtension
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly TranslatorInterface $translator
    ) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'quote',
                [$this, 'quote']
            ),
        ];
    }

    public function quote(string $text)
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

    private function markdownQuote(string $text) {
        return preg_replace('/\n/', "\n >", $text );
    }

    public function getName()
    {
        return 'quote';
    }
}
