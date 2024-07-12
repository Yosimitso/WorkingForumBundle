<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class ConfigurationTwigExtension extends AbstractExtension
{
    private array $paramList;

    public function __construct(string $themeColor)
    {
        $this->paramList = ['theme_color' => $themeColor];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'getWFParam',
                [$this, 'getWFParam']
            ),
        ];
    }

    public function getWFParam(string $param)
    {
        if (!isset($this->paramList[$param])) {
            throw new \Exception(
                'The param "' . $param . '" is missing in the WorkingForumBundle configuration'
            );
        }

        return $this->paramList[$param];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'configuration';
    }
}
