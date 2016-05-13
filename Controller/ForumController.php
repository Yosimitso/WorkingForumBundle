<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ForumController extends Controller
{
    /*
     * Display homepage of forum with subforums
     */
    public function indexAction()
    {
        $list_forum = $this->getDoctrine()->getManager()->getRepository('Yosimitso\WorkingForumBundle\Entity\Forum')->findAll();
        return $this->render('YosimitsoWorkingForumBundle:Forum:index.html.twig',array(
            'list_forum' => $list_forum
                ));
    }
    
    /*
     * Display the thread list of a subforum
     */
    public function subforumAction($subforum_slug,Request $request,$page = 1)
    {
         $allow_anonymous = $this->container->getParameter( 'yosimitso_working_forum.allow_anonymous_read' );
         $user = $this->getUser();
          $subforum = $this->getDoctrine()->getManager()->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')->findOneBySlug($subforum_slug);
         if ($user !== null || $allow_anonymous)
         {
             $forbidden = false;
        if ($page <= 0)
        {
            $page = 1;
        }
       
       $list_subforum_query = $this->getDoctrine()->getManager()->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')->findBySubforum($subforum->getId(),['pin' => 'DESC', 'lastReplyDate' => 'DESC']);
      
        $date_format = $this->container->getParameter( 'yosimitso_working_forum.date_format' );
       $paginator  = $this->get('knp_paginator');
        $list_subforum = $paginator->paginate(
        $list_subforum_query,
        $request->query->get('page', 1)/*page number*/,
        $this->container->getParameter( 'yosimitso_working_forum.thread_per_page' ) /*limit per page*/
    );
        
         }
         else
         {
           
             $forbidden = true;
             $list_subforum = $date_format = null;
             
         }
        return $this->render('YosimitsoWorkingForumBundle:Forum:thread_list.html.twig',array(
            'subforum' => $subforum,
            'thread_list' => $list_subforum,
            'date_format' => $date_format,
            'forbidden' => $forbidden
                ));
    }
}
