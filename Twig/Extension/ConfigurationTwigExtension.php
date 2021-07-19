<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
/**
 * Class ConfigurationTwigExtension
 *
 * @package Yosimitso\WorkingForumBundle\Twig\Extension
 */
class ConfigurationTwigExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $paramList;

    /**
     * @param string $themeColor
     */
    public function __construct($themeColor)
    {
        $this->paramList = ['theme_color' => $themeColor];
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'getWFParam',
                [$this, 'getWFParam']
            ),
        ];
    }

    /**
     * @param string $param
     *
     * @return mixed
     * @throws \Exception
     */
    public function getWFParam($param)
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
