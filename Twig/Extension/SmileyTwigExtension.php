<?php

namespace Yosimitso\WorkingForumBundle\Twig\Extension;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class SmileyTwigExtension extends \Twig_Extension
{
    
    private $container;
    private $listSmileys;
    public function __construct(Container $container) {
        $this->container = $container;
        $this->listSmiley = [
            ':smile:' => 'smile.png',
            ':wink:' => 'wink.png',
            ':angry:' => 'angry.png',
            ':bigsmile:' => 'big_grin.png',
            ':crying:' => 'crying.png',
            ':sad:' => 'frown.png',
            ':tongue:' => 'tongue.png',
            ':zzz:' => 'yawn.png',
            ':zipped:' => 'zipped.png',
            ':sick:' => 'sick.png',
            ':whistle:' => 'whistle.png',
            ':evil:' => 'evil.png',
            ':stress:' => 'stress.png',
            ':delicious:' => 'delicious.png',
            ':shy:' => 'bashful.png',
            ':bored:' => 'bored.png',
            ':confused:' => 'confused.png',
            ':love:' => 'heart.png',
            ':oh:' => 'oh.png',
            ':nerdy:' => 'nerdy.png',
            ':present:' => 'present.png',
            ':sun:' => 'sun.png'
           
         
            
        ];
    
    }
    public function getListSmiley()
    {
        return $this->listSmileys;
    }
      public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('smiley', array($this, 'smiley'))
        );
    }

    public function smiley($text)
    {
        $list = [
            ':)' => 'smile.png',
            ':smile:' => 'smile.png',
            ';)' => 'wink.png',
            ':wink:' => 'wink.png',
            ':P' => '',
            ':D' => '',
            
        ];
     
        foreach ($list as $key => $value)
        {
            $list[$key] = '<img src="'.$this->container->get('request')->getBasePath().'/bundles/yosimitsoworkingforum/images/smiley/'.$value.'" />';
        }
        return $this->strReplaceAssoc($list, $text);
    }
    public function getName()
    {
        return 'smiley';
    }
    
    function strReplaceAssoc(array $replace, $subject) {
   return str_replace(array_keys($replace), array_values($replace), $subject);   
    } 
}