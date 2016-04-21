<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Yosimitso\WorkingForumBundle\Form\SearchType;

class SearchController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listForum = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Forum')->findAll();
          /*  $user = $this->getUser();
  
         if ($user !== null || $allow_anonymous)
         {
             $forbidden = false;
        if ($page <= 0)
        {
            $page = 1;
        }*/
        
        $forbidden = false;
        $form = $this->createForm(new SearchType);
        $form->handleRequest($request);
        //var_dump($form);
      //  exit();
         
        if ($form->isSubmitted())
        {
            if ($form->isValid())
            {
         
              $thread_list_query = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')->search(0,100,$form['keywords']->getData());
               $date_format = $this->container->getParameter( 'yosimitso_working_forum.date_format' );
               
               $paginator  = $this->get('knp_paginator');
                $thread_list = $paginator->paginate(
        $thread_list_query,
        $request->query->get('page', 1)/*page number*/,
        $this->container->getParameter( 'yosimitso_working_forum.thread_per_page' )); /*limit per page*/
                
                 return $this->render('YosimitsoWorkingForumBundle:Forum:thread_list.html.twig',array(
            'thread_list' => $thread_list,
            'date_format' => $date_format,
            'forbidden' => $forbidden,
            'keywords' => $form['keywords']->getData()
                         ));
            }
        }
        
        
        return $this->render('YosimitsoWorkingForumBundle:Search:search.html.twig',[
                'listForum' => $listForum,
                'form' => $form->createView()
                ]);
    }
}