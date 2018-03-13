<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\File;
use Yosimitso\WorkingForumBundle\Form\MoveThreadType;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Form\ThreadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Yosimitso\WorkingForumBundle\Util\Slugify;
use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Yosimitso\WorkingForumBundle\Util\Thread as ThreadUtil;
use Yosimitso\WorkingForumBundle\Util\FileUploader as FileUploadUtil;
use Yosimitso\WorkingForumBundle\Twig\Extension\SmileyTwigExtension;

/**
 * Class ThreadController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class ThreadController extends BaseController
{
    protected $threadUtil;
    protected $fileUploaderUtil;
    protected $smileyTwigExtension;

    public function __construct(ThreadUtil $threadUtil, FileUploadUtil $fileUploaderUtil, SmileyTwigExtension $smileyTwigExtension)
    {
       $this->threadUtil = $threadUtil;
       $this->fileUploaderUtil = $fileUploaderUtil;
       $this->smileyTwigExtension = $smileyTwigExtension;
    }
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
        $subforum = $this->em->getRepository('YosimitsoWorkingForumBundle:Subforum')->findOneBySlug($subforum_slug);
        $thread = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);

        $anonymousUser = (is_null($this->user)) ? true : false;

        if (!$this->authorization->hasSubforumAccess($subforum)) { // CHECK IF USER HAS AUTHORIZATION TO VIEW THIS THREAD
            return $this->render('@YosimitsoWorkingForum/Thread/thread.html.twig',
                [
                    'subforum'    => $subforum,
                    'thread'      => $thread,
                    'forbidden'   => true,
                    'forbiddenMsg' => $this->authorization->getErrorMessage()
                ]
            );

        }
            $autolock = $this->threadUtil->isAutolock($thread); // CHECK IF THREAD IS AUTOMATICALLY LOCKED (TOO OLD?)
            $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown

            $my_post = new Post($this->user, $thread);
            $form = $this->createForm(PostType::class, $my_post); // create form for posting
            $form->handleRequest($request);

            if ($form->isSubmitted()) { // USER SUBMIT HIS POST

                if (!$anonymousUser && $this->user->isBanned()) // USER IS BANNED CAN'T POST
                {
                    $this->flashbag->add(
                        'error',
                        $this->translator->trans('message.banned', [], 'YosimitsoWorkingForumBundle')
                    )
                    ;

                    return $this->redirect($this->generateUrl('workingforum', []));
                }

                if ($autolock) // THREAD IS LOCKED CAUSE TOO OLD ACCORDING TO PARAMETERS
                {
                    $this->flashbag->add(
                        'error',
                        $this->translator->trans('thread_too_old_locked', [], 'YosimitsoWorkingForumBundle')
                    )
                    ;

                    return $this->redirect($this->generateUrl('workingforum_thread', ['subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug]));
                }

                if ($form->isValid()) {

                    $subforum->newPost($this->user); // UPDATE SUBFORUM STATISTIC
                    $thread->addReply($this->user); // UPDATE THREAD STATISTIC

                    if (!$anonymousUser) {
                        $this->user->addNbPost(1);
                        $this->em->persist($this->user);
                    }

                    $this->em->persist($thread);
                    $this->em->persist($my_post);
                    $this->em->persist($subforum);

                    $postQuery = $this->em
                        ->getRepository('YosimitsoWorkingForumBundle:Post')
                        ->findByThread($thread->getId())
                    ;

                    $post_list =  $this->threadUtil->paginate($postQuery);

                    if (!empty($form->getData()->getFilesUploaded())) {
                        $file = $this->fileUploaderUtil->upload($form->getData()->getFilesUploaded(), $my_post);
                        if (!$file) { // FILE UPLOAD FAILED

                            $this->flashbag->add(
                                'error',
                                $this->fileUploaderUtil->getErrorMessage()
                            );
                            return $this->redirect(
                                $this->generateUrl(
                                    'workingforum_thread',
                                    ['subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug, 'page' => $post_list->getPageCount()]
                                ));
                        }
                        $my_post->addFiles($file);
                    }

                    $this->em->flush();

                    $this->flashbag->add(
                        'success',
                        $this->translator->trans('message.posted', [], 'YosimitsoWorkingForumBundle')
                    );

                    return $this->redirect($this->generateUrl('workingforum_thread',
                        ['subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug, 'page' => $post_list->getPageCount() ]
                    )
                    );
                }
            }



        if ($this->authorization->hasModeratorAuthorization())
        {
            $moveThread = $this->createForm(MoveThreadType::class)->createView();
        }
        else
        {
            $moveThread = false;
        }

        $postQuery = $this->em
            ->getRepository('YosimitsoWorkingForumBundle:Post')
            ->findByThread($thread->getId())
        ;
        $post_list = $this->threadUtil->paginate($postQuery);

        $hasAlreadyVoted = $this->em->getRepository('YosimitsoWorkingForumBundle:PostVote')->getThreadVoteByUser($thread, $this->user);

        $parameters  = [ // PARAMETERS USED BY TEMPLATE
            'dateFormat' => $this->container->getParameter('yosimitso_working_forum.date_format'),
            'thresholdUsefulPost' => $this->container->getParameter('yosimitso_working_forum.vote')['threshold_useful_post'],
            'fileUpload' => $this->container->getParameter('yosimitso_working_forum.file_upload'),
            ];
        $parameters['fileUpload']['maxSize'] = $this->fileUploaderUtil->getMaxSize();

        return $this->render('@YosimitsoWorkingForum/Thread/thread.html.twig',
            [
                'subforum'    => $subforum,
                'thread'      => $thread,
                'post_list'   => $post_list,
                'parameters' => $parameters,
                'form'        => (isset($form)) ? $form->createView() : null,
                'listSmiley'  => $listSmiley,
                'forbidden'   => false,
                'request'     => $request,
                'moveThread' => $moveThread,
                'allowModeratorDeleteThread' => $this->getParameter('yosimitso_working_forum.allow_moderator_delete_thread'),
                'autolock' => $autolock,
                'hasAlreadyVoted' => $hasAlreadyVoted,
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
        if (is_null($this->user)) {
            throw new \Exception("Anonymous user aren't allowed to create threads",
                403);
        }

        $subforum = $this->em->getRepository('YosimitsoWorkingForumBundle:Subforum')->findOneBySlug($subforum_slug);

          if (!$this->authorization->hasSubforumAccess($subforum)) {
              $this->flashbag->add(
                      'error',
                      $this->translator->trans($this->authorization->getErrorMessage(), [], 'YosimitsoWorkingForumBundle')
                  )
              ;
              return $this->redirect($this->generateUrl('workingforum_forum'));

        }

        $my_thread = new Thread($this->user, $subforum);
        $my_post = new Post($this->user);
        $my_thread->addPost($my_post);

        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown
        $form = $this->createForm(ThreadType::class, $my_thread);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            if (!empty($form->getData()->getPost()[0]->getFilesUploaded())) {
                $file = $this->fileUploaderUtil->upload($form->getData()->getPost()[0]->getFilesUploaded(), $my_post);
                if (!$file) { // FILE UPLOAD FAILED

                    $this->flashbag->add(
                        'error',
                        $this->fileUploaderUtil->getErrorMessage()
                    );
                    return $this->redirect(
                        $this->generateUrl(
                            'workingforum_new_thread',
                            ['subforum_slug' => $subforum_slug]
                        ));
                }
                $my_post->addFiles($file);
            }

            $subforum->newThread($this->user); // UPDATE STATISTIC

            $this->user->addNbPost(1);
            $this->em->persist($this->user);
            $this->em->persist($my_thread);
            $this->em->persist($subforum);

            $this->em->flush();

            $my_thread->setSlug($my_thread->getId() . '-' . Slugify::convert($my_thread->getLabel())); // SLUG NEEDS THE ID

            $my_post->setThread($my_thread); // ATTACH TO THREAD
            $this->em->persist($my_post);
            $this->em->persist($my_thread);
            $this->em->flush();

            $this->flashbag->add(
                'success',
                $this->translator->trans('message.threadCreated', [], 'YosimitsoWorkingForumBundle')
            )
            ;

            return $this->redirect($this->generateUrl('workingforum_thread', ['subforum_slug' => $subforum_slug, 'thread_slug' => $my_thread->getSlug()])); // REDIRECT TO THE NEW THREAD

        }

        $parameters  = [ // PARAMETERS USED BY TEMPLATE
            'fileUpload' => $this->getParameter('yosimitso_working_forum.file_upload')
        ];
        $parameters['fileUpload']['maxSize'] = $this->fileUploaderUtil->getMaxSize();

        return $this->render('@YosimitsoWorkingForum/Thread/new.html.twig',
            [
                'subforum'   => $subforum,
                'form'       => $form->createView(),
                'listSmiley' => $listSmiley,
                'request'    => $request,
                'parameters' => $parameters
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
        $thread = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new \Exception("Thread error",
                500
            );

        }

        if (!$this->authorization->hasModeratorAuthorization() && $this->user->getId() != $thread->getAuthor()->getId()) // ONLY ADMIN MODERATOR OR THE THREAD'S AUTHOR CAN SET A THREAD AS RESOLVED
        {
            throw new \Exception('You are not authorized to do this', 403);
        }

        $thread->setResolved(true);
        $this->em->persist($thread);
        $this->em->flush();

        $this->flashbag->add(
                 'success',
                 $this->translator->trans('message.threadResolved', [], 'YosimitsoWorkingForumBundle')
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
        $thread = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new \Exception("Thread error",
                500
            );

        }

        if ($thread->getPin())
        {
            throw new \Exception("Thread already pinned",500);
        }

        $thread->setPin(true);
        $this->em->persist($thread);
        $this->em->flush();

        $this->flashbag
            ->add(
                'success',
                $this->translator->trans('message.threadPinned', [], 'YosimitsoWorkingForumBundle')
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
        $check_already = $this->em->getRepository('YosimitsoWorkingForumBundle:PostReport')
                            ->findOneBy(['user' => $this->user->getId(), 'post' => $post_id])
        ;
        $post = $this->em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById($post_id);

        if (is_null($check_already) && empty($post->getModerateReason) && !is_null($this->user)) // THE POST HASN'T BEEN REPORTED AND NOT ALREADY MODERATED
        {
            $post = $this->em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById($post_id);
            if (!is_null($post)) {
                $report = new PostReport;
                $report->setPost($post)
                       ->setUser($this->user)
                ;
                $this->em->persist($report);
                $this->em->flush();

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
        $thread = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new Exception("Thread can't be found", 500);

        }

        $thread->setLocked(true);
        $this->em->persist($thread);
        $this->em->flush();

        $this->flashbag->add(
            'success',
            $this->translator->trans('message.threadLocked', [], 'YosimitsoWorkingForumBundle')
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
        $threadId = $request->get('threadId');
        $target = $request->get('target');

        $thread = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneById($threadId);
        $current_subforum = $thread->getSubforum();
        $current_nbReplies = $thread->getNbReplies();
        $target = $this->em->getRepository('YosimitsoWorkingForumBundle:Subforum')->findOneById($target);

        if (is_null($thread) || is_null($target))
        {
            return new Reponse(null,500);
        }

        $current_subforum->setNbThread($current_subforum->getNbThread() - 1);
        $current_subforum->setNbPost($current_subforum->getNbPost() - $current_nbReplies);
        $thread->setSubforum($target);
        $target->setNbThread($target->getNbThread() + 1);
        $target->setNbPost($target->getNbPost() + $current_nbReplies);

        $this->em->persist($thread);
        $this->em->persist($current_subforum);
        $this->em->persist($target);
        $this->em->flush();

        return new Response(json_encode(['res' => 'true', 'targetLabel' => $target->getName()]), 200);
    }

        /**
         * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
         * The thread is deleted by modo or admin
         */
    public function deleteThreadAction($threadSlug)
    {
        if (!$this->getParameter('yosimitso_working_forum.allow_moderator_delete_thread'))
        {
            throw new Exception('Thread deletion is not allowed');
        }

        $thread = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')->findOneBySlug($threadSlug);
        $subforum = $this->em->getRepository('YosimitsoWorkingForumBundle:Subforum')->findOneById($thread->getSubforum()->getId());

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

        $this->em->persist($subforum);
        $this->em->remove($thread);
        $this->em->flush();

        $this->flashbag->add(
            'success',
            $this->translator->trans('message.thread_deleted', [], 'YosimitsoWorkingForumBundle')
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

}

        
