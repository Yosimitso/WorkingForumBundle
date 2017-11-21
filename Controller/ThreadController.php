<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Form\MoveThreadType;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Form\ThreadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Yosimitso\WorkingForumBundle\Util\Slugify;


/**
 * Class ThreadController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class ThreadController extends Controller
{

    /**
     * Display a thread, save a post
     *
     * Méthode pour afficher le thread et de poster un nouveau message
     *
     * @param string  $subforum_slug
     * @param string  $thread_slug
     * @param Request $request
     * @param int     $page
     *
     * @return Response
     */
    public function indexAction($subforum_slug, $thread_slug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subforum = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')->findOneBySlug($subforum_slug);
        $thread = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')->findOneBySlug($thread_slug);
        $user = $this->getUser();

        $authorizationChecker = $this->get('yosimitso_workingforum_authorization');
        if (!$authorizationChecker->hasSubforumAccess($subforum)) { // CHECK IF USER HAS AUTHORIZATION TO VIEW THIS THREAD
            return $this->render('YosimitsoWorkingForumBundle:Thread:thread.html.twig',
                [
                    'subforum'    => $subforum,
                    'thread'      => $thread,
                    'forbidden'   => true,
                    'forbiddenMsg' => $authorizationChecker->getErrorMessage()
                ]
            );

        }
            $autolock = $this->isAutoLock($thread); // CHECK IF THREAD IS AUTOMATICALLY LOCKED (TOO OLD?)

            // Smileys available for markdown
            $listSmiley = $this->get('yosimitso_workingforum_smiley')->getListSmiley();

            $paginator = $this->get('knp_paginator');

            $date_format = $this->container->getParameter('yosimitso_working_forum.date_format');

            $my_post = new Post;
            $form = $this->createForm(PostType::class, $my_post); // create form for posting
            $form->handleRequest($request);

            if ($form->isSubmitted()) { // USER SUBMIT HIS POST

                if ($user->isBanned()) // USER IS BANNED CAN'T POST
                {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        $this->get('translator')->trans('message.banned', [], 'YosimitsoWorkingForumBundle')
                    )
                    ;

                    return $this->redirect($this->generateUrl('workingforum', []));
                }

                if ($autolock) // THREAD IS LOCKED CAUSE TOO OLD ACCORDING TO PARAMETERS
                {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        $this->get('translator')->trans('thread_too_old_locked', [], 'YosimitsoWorkingForumBundle')
                    )
                    ;

                    return $this->redirect($this->generateUrl('workingforum_thread', ['subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug]));
                }

                if ($form->isValid()) {

                    $published = 1;
                    $thread->addNbReplies(1)
                           ->setLastReplyDate(new \DateTime)
                            ->setLastReplyUser($user);
                    ; // Update thread statistic

                    $my_post->setCdate(new \DateTime)
                            ->setPublished($published)
                            ->setContent($my_post->getContent())
                            ->setUser($user)
                    ;
                    $my_post->setThread($thread);

                    $subforum->setNbPost($subforum->getNbPost() + 1); // UPDATE THREAD MESSAGE COUNTER
                    $subforum->setLastReplyDate(new \DateTime)
                            ->setLastReplyUser($user);

                    $user->addNbPost(1);
                    $em->persist($user);
                    $em->persist($thread);
                    $em->persist($my_post);
                    $em->persist($subforum);

                    $em->flush();

                    $this->get('session')->getFlashBag()->add(
                        'success',
                        $this->get('translator')->trans('message.posted', [], 'YosimitsoWorkingForumBundle')
                    )
                    ;
                    $post_query = $em
                        ->getRepository('Yosimitso\WorkingForumBundle\Entity\Post')
                        ->findByThread($thread->getId())
                    ;

                    $post_list = $paginator->paginate(
                        $post_query,
                        $request->query->get('page')/*page number*/,
                        $this->container->getParameter('yosimitso_working_forum.post_per_page') /*limit per page*/
                    );

                    return $this->redirect($this->generateUrl('workingforum_thread',
                        ['subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug, 'page' => $post_list->getPageCount() ]
                    )
                    );
                }
            }



        if ($authorizationChecker->hasModeratorAuthorization())
        {
            $moveThread = $this->createForm(MoveThreadType::class)->createView();
        }
        else
        {
            $moveThread = false;
        }

        $post_query = $em
            ->getRepository('Yosimitso\WorkingForumBundle\Entity\Post')
            ->findByThread($thread->getId())
        ;

        $post_list = $paginator->paginate(
            $post_query,
            $request->query->get('page',1)/*page number*/,
            $this->container->getParameter('yosimitso_working_forum.post_per_page') /*limit per page*/
        );




        return $this->render('YosimitsoWorkingForumBundle:Thread:thread.html.twig',
            [
                'subforum'    => $subforum,
                'thread'      => $thread,
                'post_list'   => $post_list,
                'date_format' => $date_format,
                'form'        => (isset($form)) ? $form->createView() : null,
                'listSmiley'  => $listSmiley,
                'forbidden'   => false,
                'request'     => $request,
                'moveThread' => $moveThread,
                'allowModeratorDeleteThread' => $this->getParameter('yosimitso_working_forum.allow_moderator_delete_thread'),
                'autolock' => $autolock
            ]
        );

    }

    /**
     *  New thread
     * @param int     $subforum_slug
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *  new thread
     */
    public function newAction($subforum_slug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subforum = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')->findOneBySlug($subforum_slug);
        $authorizationChecker = $this->get('yosimitso_workingforum_authorization');

          if (!$authorizationChecker->hasSubforumAccess($subforum)) {
            return $this->render('YosimitsoWorkingForumBundle:Thread:thread.html.twig',
                [
                    'subforum'    => $subforum,
                    'thread'      => $thread,
                    'forbidden'   => true,
                    'forbiddenMsg' => $authorizationChecker->getErrorMessage()
                ]
            );

        }


        $my_thread = new Thread;
        $my_post = new Post;
        $my_thread->addPost($my_post);
        $user = $this->getUser();
        $listSmiley = $this->get('yosimitso_workingforum_smiley')->getListSmiley(); // Smileys available for markdown
        $form = $this->createForm(ThreadType::class, $my_thread);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $published = 1;
            $my_thread->addNbReplies(1)
                      ->setLastReplyDate(new \DateTime)
                      ->setCdate(new \DateTime)
                      ->setNbReplies(1)
                      ->setLastReplyUser($user)
            ;

            $my_thread->setSubforum($subforum);
            $my_thread->setAuthor($user);

            $em->persist($my_thread);
            $my_post->setCdate(new \DateTime)
                    ->setPublished($published)
                    ->setContent($my_post->getContent())
                    ->setUser($user)
            ;


            $subforum->setNbPost($subforum->getNbPost() + 1);
            $subforum->setNbThread($subforum->getNbThread() + 1);
            $subforum->setLastReplyDate(new \DateTime);
            $subforum->setLastReplyUser($user);

            $user->addNbPost(1);
            $em->persist($user);
            $em->persist($my_thread);
            $em->persist($subforum);

            $em->flush();

            $my_thread->setSlug($my_thread->getId() . '-' . Slugify::convert($my_thread->getLabel()));

            $my_post->setThread($my_thread);
            $em->persist($my_post);
            $em->persist($my_thread);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('message.threadCreated', [], 'YosimitsoWorkingForumBundle')
            )
            ;

            return $this->redirect($this->generateUrl('workingforum_subforum', ['subforum_slug' => $subforum_slug]));

        }

        return $this->render('YosimitsoWorkingForumBundle:Thread:new.html.twig',
            [
                'subforum'   => $subforum,
                'form'       => $form->createView(),
                'listSmiley' => $listSmiley,
                'request'    => $request,
            ]
        );
    }

    /**
     * The thread is resolved
     *
     * @param $subforum_slug
     * @param $thread_slug
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    function resolveAction($subforum_slug, $thread_slug)
    {
        $em = $this->getDoctrine()->getManager();
        $thread = $em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);
        $user = $this->getUser();
        $authorizationChecker = $this->get('yosimitso_workingforum_authorization');

        if (is_null($thread)) {
            throw new \Exception("Thread error",
                500, ""
            );

        }

        if (!$authorizationChecker->hasModeratorAuthorization() && $user->getId() != $thread->getAuthor()->getId()) // ONLY ADMIN MODERATOR OR THE THREAD'S AUTHOR CAN SET A THREAD AS RESOLVED
        {
            throw new \Exception('You are not authorized to do this', 403, '');
        }

        $thread->setResolved(true);
        $em->persist($thread);
        $em->flush();

        $this->get('session')
             ->getFlashBag()
             ->add(
                 'success',
                 $this->get('translator')->trans('message.threadResolved', [], 'YosimitsoWorkingForumBundle')
             )
        ;

        return $this->redirect(
            $this->generateUrl('workingforum_thread',
                [
                    'thread_slug'   => $thread_slug,
                    'subforum_slug' => $subforum_slug,
                ]
            )
        );
    }

    /**
     * A moderator pin a thread
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @param $subforum_slug
     * @param $thread_slug
     * @return RedirectResponse
     * @throws \Exception
     */
    function pinAction($subforum_slug, $thread_slug)
    {
        $em = $this->getDoctrine()->getManager();
        $thread = $em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new \Exception("Thread error",
                500, ""
            );

        }

        if ($thread->getPin())
        {
            throw new \Exception("Thread already pinned",500);
        }

        $thread->setPin(true);
        $em->persist($thread);
        $em->flush();

        $this->get('session')
            ->getFlashBag()
            ->add(
                'success',
                $this->get('translator')->trans('message.threadPinned', [], 'YosimitsoWorkingForumBundle')
            )
        ;

        return $this->redirect(
            $this->generateUrl('workingforum_thread',
                [
                    'thread_slug'   => $thread_slug,
                    'subforum_slug' => $subforum_slug,
                ]
            )
        );
    }

    /*
     * Report a post to the admins
     */
    /**
     * A user report a thread
     * @param $post_id
     * @return Response
     */
    function reportAction($post_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $check_already = $em->getRepository('YosimitsoWorkingForumBundle:PostReport')
                            ->findOneBy(['user' => $user->getId(), 'post' => $post_id])
        ;
        $post = $em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById($post_id);

        if (is_null($check_already) && empty($post->getModerateReason) && !is_null($user
            )
        ) // THE POST HASN'T BEEN REPORTED AND NOT ALREADY MODERATED
        {
            $post = $em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById($post_id);
            if (!is_null($post)) {
                $report = new PostReport;
                $report->setPost($post)
                       ->setUser($user)
                ;
                $em->persist($report);
                $em->flush();

                return new Response(json_encode('true'), 200);
            }
            else {
                return new Response(json_encode('false'), 500);
            }
        }
        else // ALREADY WARNED BUT THAT'S OK, THANKS ANYWAY
        {
            return new Response(json_encode('true'), 200);
        }

    }

    /**
     * The thread is locked by a moderator or admin
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     *
     * @param $subforum_slug
     * @param $thread_slug
     *
     * @return RedirectResponse
     */
    function lockAction($subforum_slug, $thread_slug)
    {
        $em = $this->getDoctrine()->getManager();
        $thread = $em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new Exception("Thread can't be found", 500, "");

        }

        $thread->setLocked(true);
        $em->persist($thread);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('message.threadLocked', [], 'YosimitsoWorkingForumBundle')
        )
        ;

        return $this->redirect(
            $this->generateUrl('workingforum_thread',
                [
                    'thread_slug'   => $thread_slug,
                    'subforum_slug' => $subforum_slug,
                ]
            )
        );

    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @param Request $request
     * @return Response|Reponse
     */
    public function moveThreadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $threadId = $request->get('threadId');
        $target = $request->get('target');

        $thread = $em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneById($threadId);
        $current_subforum = $thread->getSubforum();
        $current_nbReplies = $thread->getNbReplies();
        $target = $em->getRepository('YosimitsoWorkingForumBundle:Subforum')->findOneById($target);

        if (is_null($thread) || is_null($target))
        {
            return new Reponse(null,500);
        }

        $current_subforum->setNbThread($current_subforum->getNbThread() - 1);
        $current_subforum->setNbPost($current_subforum->getNbPost() - $current_nbReplies);
        $thread->setSubforum($target);
        $target->setNbThread($target->getNbThread() + 1);
        $target->setNbPost($target->getNbPost() + $current_nbReplies);

        $em->persist($thread);
        $em->persist($current_subforum);
        $em->persist($target);
        $em->flush();

        return new Response(json_encode(['res' => 'true', 'targetLabel' => $target->getName()]), 200);
    }

        /**
         * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
         *
         */
    public function deleteThreadAction($threadSlug)
    {
        if (!$this->getParameter('yosimitso_working_forum.allow_moderator_delete_thread'))
        {
            throw new Exception('Thread deletion is not allowed');
        }
        $em = $this->getDoctrine()->getManager();
        $thread = $em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($threadSlug);
        $subforum = $em->getRepository('YosimitsoWorkingForumBundle:Subforum')->findOneById($thread->getSubforum()->getId());

        if (is_null($thread))
        {
            throw new Exception('Thread cannot be found');
        }
        if (is_null($subforum))
        {
            throw new Exception('Thread cannot be found');
        }

        $subforum->addNbThread(-1);
        $subforum->addNbPost(-$thread->getnbReplies());

        $em->persist($subforum);
        $em->remove($thread);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('message.thread_deleted', [], 'YosimitsoWorkingForumBundle')
        )
        ;

        return $this->redirect(
            $this->generateUrl('workingforum_subforum',
                [
                    'subforum_slug' => $subforum->getSlug(),
                ]
            )
        );


    }

    private function isAutoLock($thread) {
        if ($this->getParameter('yosimitso_working_forum.lock_thread_older_than'))
        {
            $diff = $thread->getCdate()->diff(new \DateTime());
            if ($diff->days > $this->getParameter('yosimitso_working_forum.lock_thread_older_than'))
            {
                return true;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }

}

        
