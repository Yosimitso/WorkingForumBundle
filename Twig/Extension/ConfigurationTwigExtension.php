<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;


class ConfigurationTwigExtension extends \Twig_Extension
{
    
    private $paramList;
    public function __construct($themeColor) {
        $this->paramList = ['themeColor' => $themeColor];
    
    }

      public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getWFParam',[$this,'getWFParam'])
        );
    }

    public function getWFParam($param)
    {
        if (isset($this->paramList[$param]))
        {
            return paramList[$param];
        }
        else
        {
            throw new \Exception('The param "'.$param.'" is missing in the WorkingForumBundle configuration');
        }

    }
    public function getName()
    {
        return 'configuration';
    }
    
}
