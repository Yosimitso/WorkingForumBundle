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
       
        return $this->render('YosimitsoWorkingForumBundle:Admin:main.html.twig',[
                'list_forum' => $list_forum
               // 'form' => $form->createView()
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