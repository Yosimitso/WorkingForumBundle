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
      // $form_settings_builder = $this->createFormBuilder();
       
       $settings = ['allow_anonymous_read' => ['type' =>'boolean', 'value' => false],
                    'allow_moderator_delete_thread' => ['type' =>'boolean', 'value' => false]
                    ];
       $settings_render = [];
       foreach ($settings as $index => $setting)
       {
           $setting['value'] = $this->container->getParameter('yosimitso_working_forum.'.$index);
           if ($setting['type'] == 'boolean')
           {
               $attrs = ['autocomplete' => 'off', 'disabled' => 'disabled'];
            $setting_html = '<input type="checkbox" id="'.$index.'" name="'.$index.'" ';
           }
           
            foreach ($attrs as $indexAttr => $attr)
            {
              $setting_html .= $indexAttr.'="'.$attr.'" ';  
            }
            
            $setting_html .=   '/>'.$this->get('translator')->trans('setting.'.$index,[],'YosimitsoWorkingForumBundle');    
               
           $settings_render[] =  $setting_html;
                   
                  
           //$form_settings_builder->add($index,'checkbox',['required' => false, 'label' => 'setting.'.$index, 'translation_domain' => 'YosimitsoWorkingForumBundle', 'attr' => $attr ]);
           }
       
 
      
        return $this->render('YosimitsoWorkingForumBundle:Admin:main.html.twig',[
                'list_forum' => $list_forum,
                'settings_render' => $settings_render
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
         return $this->render('YosimitsoWorkingForumBundle:Admin/Forum:form.html.twig',[
                'forum' => $forum,
                'form' => $form->createView()
                ]);
    }
    
     public function addAction(Request $request)
    {
            if (!$this->get('security.context')->isGranted('ROLE_ADMIN') )
        {
            throw new \Exception('You are not authorized to do this');
        }
        $em = $this->getDoctrine()->getManager();
        $forum = new \Yosimitso\WorkingForumBundle\Entity\Forum;
       $forum->addSubForum(new \Yosimitso\WorkingForumBundle\Entity\Subforum);

        $form = $this->createForm(New AdminForumType,$forum);
        
        $form->handleRequest($request);
        
        if ($form->isValid() && $form->isSubmitted())
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
        
         return $this->render('YosimitsoWorkingForumBundle:Admin/Forum:form.html.twig',[
                'forum' => $forum,
                'form' => $form->createView()
                ]);
    }
    
   
}