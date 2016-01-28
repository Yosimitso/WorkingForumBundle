<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;
use Symfony\Component\HttpFoundation\Request;
use Yosimitso\WorkingForumBundle\Entity\Setting;


class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            throw new \Exception('You are not authorized to do this');
        }
        $em = $this->getDoctrine()->getManager();
        $list_forum = $em->getRepository('YosimitsoWorkingForumBundle:Forum')->findAll();
        //$manage_forum = new ManageForumType;
      
         // $form = $this->createForm(new AdminForum);
     //  $settings = $em->getRepository('YosimitsoWorkingForumBundle:Setting')->findAll();
       $form_settings_builder = $this->createFormBuilder();
       
       $settings = ['allow_anonymous_read' => ['type' =>'boolean', 'value' => false],
                    'allow_moderator_delete_thread' => ['type' =>'boolean', 'value' => false]
                    ];

       foreach ($settings as $index => $setting)
       {
           $setting['value'] = $this->container->getParameter('yosimitso_working_forum.'.$index);
           if ($setting['type'] == 'boolean')
           {
               $attr = ['autocomplete' => 'off', 'disabled' => 'disabled'];
               if ($setting['value'])
               {
                   $attr['checked'] = 'checked';
               }
               
               
           $form_settings_builder->add($index,'checkbox',['required' => false, 'label' => 'setting.'.$index, 'translation_domain' => 'YosimitsoWorkingForumBundle', 'attr' => $attr ]);
           }
       }
      $form_settings = $form_settings_builder->getForm();
      /*
      $form_settings->handleRequest($request);
      
      if ($form_settings->isSubmitted())
      {
           $post_settings  = $request->request->all()['form'];
      foreach ($settings as $index => $setting)
      {
          if ($setting['type'] == 'boolean')
          {
              if (array_key_exists($index, $post_settings))
              {
                   $setting['value'] = true;
              }
                else
              {
                    
                    $setting['value'] = false;
              }
         
          }
         
      }
     
      return $this->redirect($this->generateUrl('workingforum_admin'));
      }
      /*
      use Symfony\Component\Yaml\Dumper; //I'm includng the yml dumper. Then :
$ymlDump = array( 'parameters' => array( 
   'quicksign.active' => 'On', 
   'quicksign.start.off' => $startOff, 
   'quicksign.end.off' => $endOff ), 
 );
$dumper = new Dumper(); 
$yaml = $dumper->dump($ymlDump);
$path = WEB_DIRECTORY . '/../app/config/parameters.sig.yml'; 
file_put_contents($path, $yaml);*/
      
      
      
      
      
      /*
      if ($form_settings->isValid())
      {
          
      }*/
        return $this->render('YosimitsoWorkingForumBundle:Admin:main.html.twig',[
                'list_forum' => $list_forum,
                'form_settings' => $form_settings->createView()
                ]);
    }
    
    
    
    public function editAction(Request $request,$id)
    {
            if (!$this->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            throw new \Exception('You are not authorized to do this');
        }
        $em = $this->getDoctrine()->getManager();
        $forum = $em->getRepository('YosimitsoWorkingForumBundle:Forum')->find($id);
        
        $form = $this->createForm(New AdminForumType,$forum);
        
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
           foreach ($forum->getSubforum() as $subforum)
            {
                $subforum->setForum($forum);
            }
            $em->persist($forum);
            $em->flush();
            
              $this->get('session')->getFlashBag()->add(
            'success',
            'Enregistrement effectué');
            return $this->redirect($this->generateUrl('workingforum_admin'));
            
        }
         return $this->render('YosimitsoWorkingForumBundle:Admin:edit.html.twig',[
                'forum' => $forum,
                'form' => $form->createView()
                ]);
    }
    
   
}