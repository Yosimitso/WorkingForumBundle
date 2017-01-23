<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;

/**
 * Class ConfigurationTwigExtension
 *
 * @package Yosimitso\WorkingForumBundle\Twig\Extension
 */
class ConfigurationTwigExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $paramList;

    /**
     * @param $themeColor
     */
    public function __construct($theme_color)
    {
        $this->paramList = ['theme_color' => $theme_color];
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'getWFParam',
                [$this, 'getWFParam']
            ),
        ];
    }

    /**
     * @param $param
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
