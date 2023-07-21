<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SmileyTwigExtension extends AbstractExtension
{
    private string $basePath = '';

    private array $listSmiley = [
        ':smile:'      => 'smile.png',
        ':wink:'       => 'wink.png',
        ':angry:'      => 'angry.png',
        ':biggrin:'   => 'biggrin.png',
        ':crying:'     => 'crying.png',
        ':frown:'        => 'frown.png',
        ':tongue:'     => 'tongue.png',
        ':yawn:'        => 'yawn.png',
        ':zipped:'     => 'zipped.png',
        ':sick:'       => 'sick.png',
        ':whistle:'    => 'whistle.png',
        ':evil:'       => 'evil.png',
        ':stress:'     => 'stress.png',
        ':delicious:'  => 'delicious.png',
        ':bashful:'        => 'bashful.png',
        ':bored:'      => 'bored.png',
        ':confused:'   => 'confused.png',
        ':heart:'      => 'heart.png',
        ':love:'       => 'love.png',
        ':oh:'         => 'oh.png',
        ':nerdy:'      => 'nerdy.png',
        ':present:'    => 'present.png',
        ':sun:'        => 'sun.png',
        ':sunglasses:' => 'sunglasses.png',
        ':xd:'         => 'xd.png',
        ':football:'   => 'football.png',
        ':tennis:'     => 'tennis.png',
        ':basketball:' => 'basketball.png',
        ':thumbup:'    => 'thumbup.png',
        ':thumbdown:'  => 'thumbdown.png',
    ];

    public function __construct(
        protected readonly RequestStack $request_stack,
        protected readonly Packages $asset
    )
    {
        $request = $request_stack->getCurrentRequest();

        if ($request instanceof Request) {
            $this->basePath = $request->getSchemeAndHttpHost();
        } else {
            $this->basePath = '';
        }
    }

    public function getListSmiley(): array
    {
        return $this->listSmiley;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('smiley', [$this, 'smiley']),
        ];
    }

    public function smiley(string $text)
    {
        $list = [];

        foreach ($this->listSmiley as $key => $value) {
            $list[$key] =
                '<img src="'
                . $this->basePath.$this->asset->getUrl('/bundles/yosimitsoworkingforum/images/smiley/' . $value)
                . '" />';
        }

        return $this->strReplaceAssoc($list, $text);
    }

    function strReplaceAssoc(array $replace, string $subject)
    {
        return str_replace(
            array_keys($replace),
            array_values($replace),
            $subject
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'smiley';
    }
}
