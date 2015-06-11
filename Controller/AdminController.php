<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Topic;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;
use Yosimitso\WorkingForumBundle\Form\ManageForumType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $list_forum = $em->getRepository('YosimitsoWorkingForumBundle:Forum')->findAll();
        //$manage_forum = new ManageForumType;
      
         // $form = $this->createForm(new AdminForum);
       $settings = $em->getRepository('YosimitsoWorkingForumBundle:Setting')->findAll();
       $form_settings_builder = $this->createFormBuilder();
       
      /* $list_settings = [
                    0 => ['name' => 'allow_anonymous_read', 'value' => 1, 'type' => 'bool']
                        ];*/
       
       foreach ($settings as $setting)
       {
           if ($setting->getType() == 'bool')
           {
               $attr = ['autocomplete' => 'off'];
               if ($setting->getValue())
               {
                   $attr['checked'] = 'checked';
               }
               
               
           $form_settings_builder->add($setting->getName(),'checkbox',['required' => false, 'label' => 'setting.'.$setting->getName(), 'translation_domain' => 'YosimitsoWorkingForumBundle', 'attr' => $attr ]);
           }
       }
      $form_settings = $form_settings_builder->getForm();
      
      $form_settings->handleRequest($request);
      
      if ($form_settings->isSubmitted())
      {
           $post_settings  = $request->request->all()['form'];
      foreach ($settings as $setting)
      {
          if ($setting->getType() == 'bool')
          {
              if (array_key_exists($setting->getName(), $post_settings))
              {
                   $setting->setValue(1);
              }
                else
              {
                    
                    $setting->setValue(0); 
              }
           $em->persist($setting);
          }
         
      }
      $em->flush();
      return $this->redirect($this->generateUrl('workingforum_admin'));
      }
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