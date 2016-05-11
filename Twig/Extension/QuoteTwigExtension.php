<?php


namespace Yosimitso\WorkingForumBundle\Twig\Extension;


class QuoteTwigExtension extends \Twig_Extension
{
    private $em;
    private $trans;
    public function __construct($em,$trans) {  
        $this->em = $em;
        $this->trans = $trans;
    }
 
      public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('quote', array($this, 'quote'))
        );
    }

    public function quote($text)
    {         
       $content = preg_replace_callback('#\[quote=([0-9]+)\]#',
                function ($listQuote) {
          
         $post = $this->em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById((int) $listQuote[1]);
         
          if (!is_null($post))
             {
                    return '>**'.$post->getUser()->getUsername().' '.$this->trans->trans('forum.has_written',[],'YosimitsoWorkingForumBundle')." :** \r\n".$post->getContent()."\r\n";
             }
          else
          {
              return '';
          }
                },$text)
            ;
          return $content;
    }
    public function getName()
    {
        return 'quote';
    }
    
}