<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\PostVote;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Subscription;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Form\ThreadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Yosimitso\WorkingForumBundle\Service\BundleParametersService;
use Yosimitso\WorkingForumBundle\Service\FileUploaderService;
use Yosimitso\WorkingForumBundle\Twig\Extension\SmileyTwigExtension;
use Yosimitso\WorkingForumBundle\Service\ThreadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/")
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class ThreadController extends BaseController
{
    /**
     * @var FileUploaderService
     */
    protected $fileUploaderService;
    /**
     * @var SmileyTwigExtension
     */
    protected $smileyTwigExtension;
    /**
     * @var ThreadService
     */
    protected $threadService;
    
    public function __construct(FileUploaderService $fileUploaderService, SmileyTwigExtension $smileyTwigExtension, ThreadService $threadService)
    {
        $this->fileUploaderService = $fileUploaderService;
        $this->smileyTwigExtension = $smileyTwigExtension;
        $this->threadService = $threadService;
    }

    /**
     * Display a thread, save a post
     * @Route("{forum}/{subforum}/{thread}/view", name="workingforum_thread")
     * @return Response
     */
    public function indexAction(Forum $forum, Subforum $subforum, Thread $thread, Request $request)
    {
        $anonymousUser = (is_null($this->user)) ? true : false;

        $autolock = $this->threadService->isAutolock($thread); // CHECK IF THREAD IS AUTOMATICALLY LOCKED (TOO OLD?)
        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown

        $post = new Post($this->user, $thread);

        if (!$this->bundleParameters->thread_subscription['enable']) { // SUBSCRIPTION SYSTEM DISABLED
            $canSubscribeThread = false;
        } else {
            $canSubscribeThread = (empty($this->em->getRepository(Subscription::class)->findBy(['thread' => $thread, 'user' => $this->user]))); // HAS ALREADY SUBSCRIBED ?
        }

        if (!$anonymousUser) {
            $form = $this->createForm(PostType::class, $post, ['canSubscribeThread' => $canSubscribeThread]); // create form for posting
            $form->handleRequest($request);

            if ($form->isSubmitted()) { // USER SUBMIT HIS POST

                if (!$anonymousUser && $this->user->isBanned()) // USER IS BANNED CAN'T POST
                {
                    $this->flashbag->add(
                        'error',
                        $this->translator->trans('message.banned', [], 'YosimitsoWorkingForumBundle')
                    );

                    return $this->redirect($this->generateUrl('workingforum', []));
                }

                if ($autolock && !$this->authorizationGuard->hasModeratorAuthorization()) // THREAD IS LOCKED CAUSE TOO OLD ACCORDING TO PARAMETERS
                {
                    $this->flashbag->add(
                        'error',
                        $this->translator->trans('thread_too_old_locked', [], 'YosimitsoWorkingForumBundle')
                    );

                    return $this->threadService->redirectToThread($forum, $subforum, $thread);
                }

                if ($form->isValid() && !$anonymousUser) {

                    try {
                        $this->threadService->post($subforum, $thread, $post, $this->user, $form);
                        $this->flashbag->add(
                            'success',
                            $this->translator->trans('message.posted', [], 'YosimitsoWorkingForumBundle')
                        );
                        $postQuery = $this->em
                            ->getRepository(Post::class)
                            ->findByThread($thread->getId());

                        $post_list = $this->threadService->paginate($postQuery);

                        return $this->threadService->redirectToThread($forum, $subforum, $thread, $post_list->getPageCount());
                    } catch (\Exception $e) {
                        $this->flashbag->add(
                            'error',
                            $e->getMessage()
                        );
                    }
                } else {
                    $this->flashbag->add(
                        'error',
                        $this->translator->trans('message.error.post_error', [], 'YosimitsoWorkingForumBundle')
                    );

                }
            }
        }

        $postQuery = $this->em
            ->getRepository(Post::class)
            ->findByThread($thread->getId());
        $post_list = $this->threadService->paginate($postQuery);

        $hasAlreadyVoted = $this->em->getRepository(PostVote::class)->getThreadVoteByUser($thread, $this->user);

        $parameters = [ // PARAMETERS USED BY TEMPLATE
            'dateFormat' => $this->bundleParameters->date_format,
            'timeFormat' => $this->bundleParameters->time_format,
            'thresholdUsefulPost' => $this->bundleParameters->vote['threshold_useful_post'],
            'fileUpload' => $this->bundleParameters->file_upload,
            'allowModeratorDeleteThread' => $this->bundleParameters->allow_moderator_delete_thread
        ];
        $parameters['fileUpload']['maxSize'] = $this->fileUploaderService->getMaxSize();


        $actionsAvailables = $this->threadService->getAvailableActions($this->user, $thread, $autolock, $canSubscribeThread);
        $subscripted = $this->em->getRepository(Subscription::class)->findOneBy(['thread' => $thread, 'user' => $this->user]);

        return $this->render('@YosimitsoWorkingForum/Thread/thread.html.twig',
            [
                'forum' => $forum,
                'subforum' => $subforum,
                'thread' => $thread,
                'post_list' => $post_list,
                'parameters' => $parameters,
                'form' => (isset($form)) ? $form->createView() : null,
                'listSmiley' => $listSmiley,
                'forbidden' => false,
                'request' => $request,
                'autolock' => $autolock,
                'hasAlreadyVoted' => $hasAlreadyVoted,
                'actionsAvailables' => $actionsAvailables,
                'hasSubscribed' => (is_null($subscripted)) ? false : true
            ]
        );

    }


    /**
     * New thread
     *
     * @Route("{forum}/{subforum}/new", name="workingforum_new_thread")
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function newAction(Forum $forum, Subforum $subforum, Request $request)
    {
        if (is_null($this->user)) { //ANONYMOUS CAN'T POST
            throw new AccessDeniedHttpException("access denied");
        }

        $thread = new Thread($this->user, $subforum);
        $post = new Post($this->user);
        $thread->addPost($post);

        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown
        $form = $this->createForm(ThreadType::class, $thread, ['hasModeratorAuthorization' => $this->authorizationGuard->hasModeratorAuthorization()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->threadService->create($form, $post, $thread, $subforum);

                $this->flashbag->add(
                    'success',
                    $this->translator->trans('message.threadCreated', [], 'YosimitsoWorkingForumBundle')
                );

                return $this->threadService->redirectToThread($forum, $subforum, $thread); // REDIRECT TO THE NEW THREAD

            } catch (\Exception $e) {
                $this->flashbag->add(
                    'error',
                    $e->getMessage()
                );
            }
        }

        $parameters = [ // PARAMETERS USED BY TEMPLATE
            'fileUpload' => $this->bundleParameters->file_upload
        ];
        $parameters['fileUpload']['maxSize'] = $this->fileUploaderService->getMaxSize();

        return $this->render('@YosimitsoWorkingForum/Thread/new.html.twig',
            [
                'forum' => $forum,
                'subforum' => $subforum,
                'form' => $form->createView(),
                'listSmiley' => $listSmiley,
                'request' => $request,
                'parameters' => $parameters
            ]
        );
    }

    /**
     * The thread is resolved
     * @Route("{forum}/{subforum}/{thread}/resolved", name="workingforum_resolve_thread")
     * @return RedirectResponse
     * @throws \Exception
     */
    public function resolveAction(Forum $forum, Subforum $subforum, Thread $thread)
    {
        if (!$this->authorizationGuard->hasModeratorAuthorization() && $this->user->getId() != $thread->getAuthor()->getId()) // ONLY ADMIN MODERATOR OR THE THREAD'S AUTHOR CAN SET A THREAD AS RESOLVED
        {
            throw new AccessDeniedHttpException('You are not authorized to do this');
        }

        $this->threadService->resolve($thread);

        $this->flashbag->add(
            'success',
            $this->translator->trans('message.threadResolved', [], 'YosimitsoWorkingForumBundle')
        );

        return $this->threadService->redirectToThread($forum, $subforum, $thread);
    }

    /**
     * A moderator pin a thread
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
     * @Route("{forum}/{subforum}/{thread}/pin", name="workingforum_pin_thread")
     * @return RedirectResponse
     * @throws \Exception
     */
    public function pinAction(Forum $forum, Subforum $subforum, Thread $thread)
    {
        if ($thread->getPin()) {
            throw new \Exception("Thread already pinned", 500);
        }

        $this->threadService->pin($thread);
        $this->flashbag
            ->add(
                'success',
                $this->translator->trans('message.threadPinned', [], 'YosimitsoWorkingForumBundle')
            );

        return $this->threadService->redirectToThread($forum, $subforum, $thread);
    }

    /**
     * A moderator unpin a thread
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
     * @Route("{forum}/{subforum}/{thread}/unpin", name="workingforum_unpin_thread")
     * @return RedirectResponse
     * @throws \Exception
     */
    public function unpinAction(Forum $forum, Subforum $subforum, Thread $thread)
    {
        if (!$thread->getPin()) {
            throw new \Exception("Thread not pinned", 500);
        }

        $thread->setPin(false);
        $this->em->persist($thread);
        $this->em->flush();

        $this->flashbag
            ->add(
                'success',
                $this->translator->trans('message.threadUnpinned', [], 'YosimitsoWorkingForumBundle')
            );

        return $this->threadService->redirectToThread($forum, $subforum, $thread);
    }

    /**
     * A user report a post
     * @Route("{forum}/{subforum}/report/{post}", name="workingforum_report_post", requirements={"post_id":"\d+"})
     * @return Response
     * @throws \Exception
     */
    public function reportAction(Post $post)
    {
        if (is_null($this->user)) {
            throw new AccessDeniedHttpException("access denied");
        }

        $check_already = $this->em->getRepository(PostReport::class)
            ->findOneBy(['user' => $this->user->getId(), 'post' => $post->getId()]);

        if (!is_null($check_already)) { // ALREADY WARNED BUT THAT'S OK, THANKS ANYWAY
            return new JsonResponse('true', 200);
        }

        $post = $this->em->getRepository(Post::class)->findOneById($post->getId());

        if ($this->threadService->report($post)) {
            return new JsonResponse('true', 200);
        } else {
            return new JsonResponse('false', 200);
        }
    }

    /**
     * The thread is deleted by modo or admin
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
     * @Route("{forum}/{subforum}/deletethread/{thread}", name="workingforum_delete_thread")
     * @return RedirectResponse
     */
    public function deleteThreadAction(Forum $forum, Subforum $subforum, Thread $thread)
    {
        if (!$this->bundleParameters->allow_moderator_delete_thread) {
            throw new Exception('Thread deletion is not allowed');
        }

        $this->threadService->delete($thread, $subforum);
        $this->flashbag->add(
            'success',
            $this->translator->trans('message.thread_deleted', [], 'YosimitsoWorkingForumBundle')
        );

        return $this->threadService->redirectToSubforum($forum, $subforum);
    }

    /**
     * The thread is locked by a moderator or admin
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
     * @Route("{forum}/{subforum}/{thread}/lock", name="workingforum_lock_thread")
     * @return RedirectResponse
     */
    public function lockAction(Forum $forum, Subforum $subforum, Thread $thread)
    {
        $this->threadService->lock($thread);
        $this->flashbag->add(
            'success',
            $this->translator->trans('message.threadLocked', [], 'YosimitsoWorkingForumBundle')
        );

        return $this->threadService->redirectToThread($forum, $subforum, $thread);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
     * @Route("movethread", name="workingforum_move_thread", methods="POST")
     * @param Request $request
     * @return Response
     */
    public function moveThreadAction(Request $request)
    {
        $threadId = $request->get('threadId');
        $targetId = $request->get('target');

        $thread = $this->em->getRepository(Thread::class)->findOneById($threadId);
        $currentSubforum = $thread->getSubforum();
        $targetSubforum = $this->em->getRepository(Subforum::class)->findOneById($targetId);

        if (is_null($thread) || is_null($targetSubforum)) {
            return new Response(null, 500);
        }

        $this->threadService->move($thread, $currentSubforum, $targetSubforum);

        return new JsonResponse(['res' => 'true', 'targetLabel' => $targetSubforum->getName()], 200);
    }

    /**
     * @Route("{forum}/{subforum}/cancelsubscription/{thread}", name="workingforum_cancel_subscription")
     * @return Response
     */
    public function cancelSubscriptionAction(Thread $thread)
    {
        if (is_null($this->user)) {
            return new Response(null, 500);
        }

        $subscription = $this->em->getRepository(Subscription::class)->findOneBy(['user' => $this->user, 'thread' => $thread]);

        if (!is_null($subscription)) {
            $this->em->remove($subscription);
            $this->em->flush();
            return new Response(null, 200);
        } else {
            return new Response(null, 500);
        }

    }

    /**
     * An user wants to subscribe to a thread
     * @Route("{forum}/{subforum}/addsubscription/{thread}", name="workingforum_add_subscription")
     * @return Response
     */
    public function addSubscriptionAction(Thread $thread)
    {
        if (is_null($this->user)) {
            return new Response(null, 500);
        }

        $checkSubscription = $this->em->getRepository(Subscription::class)->findOneBy(['user' => $this->user, 'thread' => $thread]);

        if (is_null($checkSubscription)) {
            $subscription = new Subscription($thread, $this->user);
            $this->em->persist($subscription);
            $this->em->flush();

            return new Response(null, 200);
        } else {

            return new Response(null, 500);
        }

    }
}

        
