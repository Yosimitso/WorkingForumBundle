<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
/**
 * @Security("has_role('ROLE_ADMIN')")
 */

class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
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
        
           $newPostReported = count($em->getRepository('YosimitsoWorkingForumBundle:PostReport')->findBy(['processed' => null]));
          /* echo '<pre>';
           \Doctrine\Common\Util\Debug::dump($user);
           echo '</pre>';*/
      
        return $this->render('YosimitsoWorkingForumBundle:Admin:main.html.twig',[
                'list_forum' => $list_forum,
                'settings_render' => $settings_render,
                'newPostReported' => $newPostReported
                ]);
    }
    
    
    
    public function editAction(Request $request,$id)
    {
            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
        {
            throw new \Exception('You are not authorized to do this');
        }
        $em = $this->getDoctrine()->getManager();
        $forum = $em->getRepository('YosimitsoWorkingForumBundle:Forum')->find($id);
      
        $statistics = ['nbThread' => 0, 'nbPost' => 0];
        foreach ($forum->getSubforum() as $subforum)
        {
            $statistics['nbThread'] += $subforum->getNbThread();
            $statistics['nbPost'] += $subforum->getNbPost();
        }
        
        
        $statistics['averagePostThread'] = ($statistics['nbThread'] > 0) ? $statistics['nbPost']/$statistics['nbThread'] : 0 ;
        $form = $this->createForm(AdminForumType::class,$forum);
        
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
           $this->get('translator')->trans('message.saved',[],'YosimitsoWorkingForumBundle'));
            return $this->redirect($this->generateUrl('workingforum_admin'));
            
        }
         return $this->render('YosimitsoWorkingForumBundle:Admin/Forum:form.html.twig',[
                'forum' => $forum,
                'form' => $form->createView(),
                'statistics' => $statistics
                ]);
    }
    
     public function addAction(Request $request)
    {
            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
        {
            throw new \Exception('You are not authorized to do this');
        }
        $em = $this->getDoctrine()->getManager();
        $forum = new \Yosimitso\WorkingForumBundle\Entity\Forum;
       $forum->addSubForum(new \Yosimitso\WorkingForumBundle\Entity\Subforum);

        $form = $this->createForm(AdminForumType::class,$forum);
        
        $form->handleRequest($request);
        
        if ($form->isValid() && $form->isSubmitted())
        {     
           foreach ($forum->getSubforum() as $subforum)
            {
                $subforum->setForum($forum);
            }
            $forum->genSlug($forum->getName());
            $em->persist($forum);
            $em->flush();
            
              $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('message.saved',[],'YosimitsoWorkingForumBundle'));
            return $this->redirect($this->generateUrl('workingforum_admin'));     
        }
        
         return $this->render('YosimitsoWorkingForumBundle:Admin/Forum:form.html.twig',[
                'forum' => $forum,
                'form' => $form->createView()
                ]);
    }
    
    public function ReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postReportList = $em->getRepository('YosimitsoWorkingForumBundle:PostReport')->findBy([],['processed' => 'ASC','id' => 'ASC']);
        $date_format = $this->container->getParameter( 'yosimitso_working_forum.date_format' );
        return $this->render('YosimitsoWorkingForumBundle:Admin/Report:report.html.twig',[
                 'postReportList' => $postReportList,
                 'date_format' => $date_format
                ]);
    }
       
    public function ReportActionGoodAction(Request $request)
    {
         $em = $this->getDoctrine()->getManager();
         
          $id = (int) htmlentities($request->request->get('id'));  
      
      
      
          
             if ($id)
          {
          $report = $em->getRepository('YosimitsoWorkingForumBundle:PostReport')->findOneById($id);
            if (is_null($report))
          {
           return new Response(json_encode('fail'),500);   
          }
          $report->setProcessed(1);
          $em->persist($report);
          }
          $em->flush();
          return new Response(json_encode('ok'),200);   
       
    }
    
    public function userListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usersList = $em->getRepository('YosimitsoWorkingForumBundle:User')->findAll(); 
      
        return $this->render('YosimitsoWorkingForumBundle:Admin/User:userslist.html.twig',[
                 'usersList' => $usersList
                
                ]);
        
    }
    
        /**
 * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
 */
    public function ReportActionModerateAction(Request $request)
    {
         $em = $this->getDoctrine()->getManager();
         $reason = htmlentities($request->request->get('reason'));
         $id = (int) htmlentities($request->request->get('id'));
         $postId = (int) htmlentities($request->request->get('postId'));
         $banuser = (int) htmlentities($request->request->get('banuser'));
           
         if (empty($reason))
         {
           return new Response(json_encode('fail'),500);    
         }
      
          $post = $em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById($postId);
          if (is_null($post))
          {
           return new Response(json_encode('fail'),500);   
          }
          $post->setModerateReason($reason);
          $em->persist($post);
         
          if ($id)
          {
          $report = $em->getRepository('YosimitsoWorkingForumBundle:PostReport')->findOneById($id);
            if (is_null($report))
          {
           return new Response(json_encode('fail'),500);   
          }
          $report->setProcessed(1);
          $em->persist($report);
          }
          
          
          if ($banuser)
          {
              $postUser = $em->getRepository('YosimitsoWorkingForumBundle:User')->findOneById($post->getUser()->getId());
                if (is_null($postUser))
                {
                    return new Response(json_encode('fail'),500);   
                }
              $postUser->setBanned(1);
              $em->persist($postUser);
          }
          $em->flush();
          return new Response(json_encode('ok'),200);   
       
    }
    
    public function deleteForumAction($forum_id)
    {
        $em = $this->getDoctrine()->getManager();
        $forum = $em->getRepository('YosimitsoWorkingForumBundle:Forum')->findOneById($forum_id);
        
        if (!is_null($forum))
        {
            $em->remove($forum);
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('admin.forumDeleted',[],'YosimitsoWorkingForumBundle'));
        }
        
        return $this->forward('YosimitsoWorkingForumBundle:Admin:index',[]);
    }
            

    
   
}
