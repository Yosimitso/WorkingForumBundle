<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class SmileyTwigExtension
 *
 * @package Yosimitso\WorkingForumBundle\Twig\Extension
 */
class SmileyTwigExtension extends \Twig_Extension
{
    /**
     * @var
     */
    private $asset;

    /**
     * @var string
     */
    private $basePath = '';

    /**
     * @var array
     */
    private $listSmiley = [
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

    /**
     * SmileyTwigExtension constructor.
     *
     * @param RequestStack $request_stack
     * @param              $asset
     */
    public function __construct(
        RequestStack $request_stack,
        $asset
    )
    {
        $request = $request_stack->getCurrentRequest();
        $this->asset = $asset;

        if ($request instanceof Request) {
            $this->basePath = $request->getBasePath();
        }
    }

    /**
     * @return array
     */
    public function getListSmiley()
    {
        return $this->listSmiley;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('smiley', [$this, 'smiley']),
        ];
    }

    /**
     * @param $text
     *
     * @return mixed
     */
    public function smiley($text)
    {
        $list = [];

        foreach ($this->listSmiley as $key => $value) {
            $list[$key] =
                '<img src="'
                . $this->asset->getAssetUrl('/bundles/yosimitsoworkingforum/images/smiley/' . $value)
                . '" />';
        }

        return $this->strReplaceAssoc($list, $text);
    }

    /**
     * @param array $replace
     * @param       $subject
     *
     * @return mixed
     */
    function strReplaceAssoc(array $replace, $subject)
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