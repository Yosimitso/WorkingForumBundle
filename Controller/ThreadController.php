<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Form\ThreadType;
use Symfony\Component\HttpFoundation\Request;

class ThreadController extends Controller
{
 /**
  * 
  * @param string $subforum_slug
  * @param string $thread_slug
  * @param Request $request
  * Méthode pour afficher le thread et de poster un nouveau message
  */
    public function indexAction($subforum_slug,$thread_slug, Request $request, $page = 1)
    {
         $em = $this->getDoctrine()->getManager();
    $thread = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')->findOneBySlug($thread_slug);
    $post_query = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Post')->findByThread($thread->getId());
    $subforum = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')->findOneBySlug($subforum_slug);
	
    
     $paginator  = $this->get('knp_paginator');
        $post_list = $paginator->paginate(
        $post_query,
        $request->query->get('page', 1)/*page number*/,
        $this->container->getParameter( 'yosimitso_working_forum.post_per_page' ) /*limit per page*/
    );
   $date_format = $this->container->getParameter( 'yosimitso_working_forum.date_format' );
   $user = $this->getUser();
    
    $my_post = new Post;
    $form = $this->createForm(new PostType, $my_post);
    $form->handleRequest($request);
    
    if ($form->isValid())
    {

	   
        $published = 1;
        $thread->addNbReplies(1)
               ->setLastReplyDate(new \DateTime);
        
        $my_post->setCdate(new \DateTime)
                ->setPublished($published)
                ->setContent(nl2br($my_post->getContent()))
                ->setUser($user);
         $my_post->setThread($thread);
         
   
         $subforum->setNbPost($subforum->getNbPost()+1);
		 $subforum->setLastReplyDate(new \DateTime);
		
      
        $user->addNbPost(1);
       $em->persist($user);
       $em->persist($thread);
       $em->persist($my_post);
       $em->persist($subforum);
       
       
       
       $em->flush();
       
        $this->get('session')->getFlashBag()->add(
            'success',
            'Votre message a bien été posté');
        return $this->redirect($this->generateUrl('workingforum_thread',['subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug]));
    }
    
     
        return $this->render('YosimitsoWorkingForumBundle:Thread:thread.html.twig',array(
            'subforum' => $subforum,
            'thread' => $thread,
            'post_list' => $post_list,
            'date_format' => $date_format,
            'form' => $form->createView()
                ));   
        
        
    }
    
    /**
     * 
     * @param int $subforum_slug
     * @param Request $request
     * @return redirect
     *  Création d'un nouveau thread
     */
    public function newAction($subforum_slug, Request $request) 
    {
       $em = $this->getDoctrine()->getManager();
        $subforum = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')->findOneBySlug($subforum_slug);
        $my_thread = new Thread;
        $my_post = new Post;
        $my_thread->addPost($my_post);
        $user = $this->getUser();
    $form = $this->createForm(new ThreadType, $my_thread);
    $form->handleRequest($request);
    
    if ($form->isValid() && $user)
    {
        $published = 1;
        $my_thread->addNbReplies(1)
               ->setLastReplyDate(new \DateTime)
                ->setCdate(new \DateTime)
                ->setNbReplies(1);
                
        $my_thread->setSubforum($subforum);
        $my_thread->setAuthor($user);
        
        $em->persist($my_thread);
        $my_post->setCdate(new \DateTime)
                ->setPublished($published)
                ->setContent(nl2br($my_post->getContent()))
                ->setUser($user);
        $my_post->setThread($my_thread);
		
		 $subforum->setNbPost($subforum->getNbPost()+1);
                 $subforum->setNbThread($subforum->getNbThread()+1);
		 $subforum->setLastReplyDate(new \DateTime);
      
        $user->addNbPost(1);
       $em->persist($user);
       $em->persist($my_thread);
       $em->persist($subforum);
	   
       $em->flush();
       
       $my_thread->setSlug($my_thread->getId().'-'.$this->clean($my_thread->getLabel()));
       
       $my_post->setThread($my_thread);
       $em->persist($my_post);
       $em->persist($my_thread);
       $em->flush();
       
        $this->get('session')->getFlashBag()->add(
            'success',
            'Votre sujet a bien été crée');
        return $this->redirect($this->generateUrl('workingforum_subforum',['subforum_slug' => $subforum_slug]));
       
    }
    
        return $this->render('YosimitsoWorkingForumBundle:Thread:new.html.twig',array(
            'subforum' => $subforum,
            'form' => $form->createView()
                ));
    }
    
        function clean ($str)
{
	/** Mise en minuscules (chaîne utf-8 !) */
	$str = mb_strtolower($str, 'utf-8');
	/** Nettoyage des caractères */
	mb_regex_encoding('utf-8');
	$str = trim(preg_replace('/ +/', ' ', mb_ereg_replace('[^a-zA-Z\p{L}]+', ' ', $str)));
	/** strtr() sait gérer le multibyte */
	$str = strtr($str, array(
	' ' => '-', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'a'=>'a', 'a'=>'a', 'a'=>'a', 'ç'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'c'=>'c', 'd'=>'d', 'd'=>'d', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'e'=>'e', 'g'=>'g', 'g'=>'g', 'g'=>'g', 'h'=>'h', 'h'=>'h', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', 'i'=>'i', '?'=>'i', 'j'=>'j', 'k'=>'k', '?'=>'k', 'l'=>'l', 'l'=>'l', 'l'=>'l', '?'=>'l', 'l'=>'l', 'ñ'=>'n', 'n'=>'n', 'n'=>'n', 'n'=>'n', '?'=>'n', '?'=>'n', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'o'=>'o', 'o'=>'o', 'o'=>'o', 'œ'=>'o', 'ø'=>'o', 'r'=>'r', 'r'=>'r', 's'=>'s', 's'=>'s', 's'=>'s', 'š'=>'s', '?'=>'s', 't'=>'t', 't'=>'t', 't'=>'t', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'u'=>'u', 'w'=>'w', 'ý'=>'y', 'ÿ'=>'y', 'y'=>'y', 'z'=>'z', 'z'=>'z', 'ž'=>'z'
	));
	return $str;
}
        function lockAction($subforum_slug,$thread_slug)
        {
            $em = $this->getDoctrine()->getManager();
            $thread = $em->getRepository('CharlyForumBundle:Thread')->findOneBySlug($thread_slug);
            if (is_null($thread))
            {
                throw new Exception("Thread can't be found", 500, "");
                
            }
            
            $thread->setLocked(true);
            $em->persist($thread);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add(
            'success',
            'Le sujet a été verrouillé');
            
            
            return $this->redirect($this->generateUrl('workingforum_thread',array('thread_slug' => $thread_slug, 'subforum_slug' => $subforum_slug)));
            
            
        }
        
        function resolveAction ($subforum_slug,$thread_slug)
        {
            $em = $this->getDoctrine()->getManager();
            $thread = $em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);
            if (is_null($thread))
            {
                throw new Exception("Thread can't be found",
                        500, "");
                
            }
            
            $thread->setResolved(true);
            $em->persist($thread);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add(
            'success',
            'Le sujet est résolu, merci pour votre retour');
            
            
            return $this->redirect($this->generateUrl('workingforum_thread',array('thread_slug' => $thread_slug, 'subforum_slug' => $subforum_slug)));
        }
}

        