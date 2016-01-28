<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ForumController extends Controller
{
    public function indexAction()
    {
        $list_forum = $this->getDoctrine()->getManager()->getRepository('Yosimitso\WorkingForumBundle\Entity\Forum')->findAll();
        return $this->render('YosimitsoWorkingForumBundle:Forum:index.html.twig',array(
            'list_forum' => $list_forum
                ));
    }
    
    public function subforumAction($subforum_slug,Request $request,$page = 1)
    {
        if ($page <= 0)
        {
            $page = 1;
        }
        $subforum = $this->getDoctrine()->getManager()->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')->findOneBySlug($subforum_slug);
       $list_subforum_query = $this->getDoctrine()->getManager()->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')->findBySubforum($subforum->getId(),['pin' => 'DESC', 'lastReplyDate' => 'DESC']);
      
        $date_format = $this->container->getParameter( 'yosimitso_working_forum.date_format' );
       $paginator  = $this->get('knp_paginator');
        $list_subforum = $paginator->paginate(
        $list_subforum_query,
        $request->query->get('page', 1)/*page number*/,
        $this->container->getParameter( 'yosimitso_working_forum.thread_per_page' ) /*limit per page*/
    );
        
        
        return $this->render('YosimitsoWorkingForumBundle:Forum:thread_list.html.twig',array(
            'subforum' => $subforum,
            'thread_list' => $list_subforum,
            'date_format' => $date_format
                ));
    }
}
