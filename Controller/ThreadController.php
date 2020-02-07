<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
use Yosimitso\WorkingForumBundle\Service\FileUploaderService;
use Yosimitso\WorkingForumBundle\Twig\Extension\SmileyTwigExtension;
use Yosimitso\WorkingForumBundle\Service\ThreadService;

/**
 * Class ThreadController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class ThreadController extends BaseController
{
    protected $fileUploaderService;
    protected $smileyTwigExtension;
    protected $threadService;

    public function __construct(FileUploaderService $fileUploaderService, SmileyTwigExtension $smileyTwigExtension, ThreadService $threadService)
    {
        $this->fileUploaderService = $fileUploaderService;
        $this->smileyTwigExtension = $smileyTwigExtension;
        $this->threadService = $threadService;
    }

    /**
     * Display a thread, save a post
     *
     * @param $forum_slug
     * @param string $subforum_slug
     * @param string $thread_slug
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction($forum_slug, $subforum_slug, $thread_slug, Request $request)
    {
        $forum = $this->em->getRepository(Forum::class)->findOneBySlug($forum_slug);
        if (is_null($forum)) {
            throw new NotFoundHttpException('Forum not found');
        }

        $subforum = $this->em->getRepository(Subforum::class)->findOneBy(['slug' => $subforum_slug, 'forum' => $forum]);
        $thread = $this->em->getRepository(Thread::class)->findOneBySlug($thread_slug);

        if (is_null($thread) || is_null($subforum)) {
            throw new NotFoundHttpException('Thread not found');
        }

        $anonymousUser = (is_null($this->user)) ? true : false;
        
        if (!$this->authorization->hasSubforumAccess($subforum)) { // CHECK IF USER HAS AUTHORIZATION TO VIEW THIS THREAD
            return $this->templating->renderResponse('@YosimitsoWorkingForum/Thread/thread.html.twig',
                [
                    'forum' => $forum,
                    'subforum' => $subforum,
                    'thread' => $thread,
                    'forbidden' => true,
                    'forbiddenMsg' => $this->authorization->getErrorMessage()
                ]
            );

        }
        $autolock = $this->threadService->isAutolock($thread); // CHECK IF THREAD IS AUTOMATICALLY LOCKED (TOO OLD?)
        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown

        $post = new Post($this->user, $thread);

        if (!$this->getParameter('yosimitso_working_forum.thread_subscription')['enable']) { // SUBSCRIPTION SYSTEM DISABLED
            $canSubscribeThread = false;
        } else {
            $canSubscribeThread = (empty($this->em->getRepository(Subscription::class)->findBy(['thread' => $thread, 'user' => $this->user]))); // HAS ALREADY SUBSCRIBED ?
        }

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

            if ($autolock && !$this->authorization->hasModeratorAuthorization()) // THREAD IS LOCKED CAUSE TOO OLD ACCORDING TO PARAMETERS
            {
                $this->flashbag->add(
                    'error',
                    $this->translator->trans('thread_too_old_locked', [], 'YosimitsoWorkingForumBundle')
                );

                return $this->redirect($this->generateUrl('workingforum_thread', ['forum_slug' => $forum_slug, 'subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug]));
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

                    return $this->redirect($this->generateUrl('workingforum_thread',
                        ['forum_slug' => $forum_slug, 'subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug, 'page' => $post_list->getPageCount()]
                    )
                    );
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

        $postQuery = $this->em
            ->getRepository(Post::class)
            ->findByThread($thread->getId());
        $post_list = $this->threadService->paginate($postQuery);

        $hasAlreadyVoted = $this->em->getRepository(PostVote::class)->getThreadVoteByUser($thread, $this->user);

        $parameters = [ // PARAMETERS USED BY TEMPLATE
            'dateFormat' => $this->getParameter('yosimitso_working_forum.date_format'),
            'timeFormat' => $this->getParameter('yosimitso_working_forum.time_format'),
            'thresholdUsefulPost' => $this->getParameter('yosimitso_working_forum.vote')['threshold_useful_post'],
            'fileUpload' => $this->getParameter('yosimitso_working_forum.file_upload'),
            'allowModeratorDeleteThread' => $this->getParameter('yosimitso_working_forum.allow_moderator_delete_thread')
        ];
        $parameters['fileUpload']['maxSize'] = $this->fileUploaderService->getMaxSize();


        $actionsAvailables = $this->threadService->getAvailableActions($this->user, $thread, $autolock, $canSubscribeThread);
        $subscripted = $this->em->getRepository(Subscription::class)->findOneBy(['thread' => $thread, 'user' => $this->user]);
        
        return $this->templating->renderResponse('@YosimitsoWorkingForum/Thread/thread.html.twig',
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
     * @param $forum_slug
     * @param $subforum_slug
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Exception
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction($forum_slug, $subforum_slug, Request $request)
    {

        $forum = $this->em->getRepository(Forum::class)->findOneBySlug($forum_slug);

        if (is_null($forum)) {
            throw new NotFoundHttpException('Forum not found');
        }

        $subforum = $this->em->getRepository(Subforum::class)->findOneBySlug($subforum_slug);

        if (is_null($subforum)) {
            throw new NotFoundHttpException('Subforum not found');
        }

        if (!$this->authorization->hasSubforumAccess($subforum)) {
            $this->flashbag->add(
                'error',
                $this->translator->trans($this->authorization->getErrorMessage(), [], 'YosimitsoWorkingForumBundle')
            );
            return $this->redirect($this->generateUrl('workingforum_forum'));

        }

        $thread = new Thread($this->user, $subforum);
        $post = new Post($this->user);
        $thread->addPost($post);

        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown
        $form = $this->createForm(ThreadType::class, $thread, ['hasModeratorAuthorization' => $this->authorization->hasModeratorAuthorization()]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->threadService->create($form, $post, $thread, $subforum);
            } catch (\Exception $e) {
                $this->flashbag->add(
                    'error',
                    $e->getMessage()
                );

                return $this->redirect(
                    $this->generateUrl(
                        'workingforum_new_thread',
                        ['form' => $form->createView(), 'forum_slug' => $forum->getSlug(), 'subforum_slug' => $subforum->getSlug()]
                    ));
            }

            $this->flashbag->add(
                'success',
                $this->translator->trans('message.threadCreated', [], 'YosimitsoWorkingForumBundle')
            );

            return $this->redirect($this->generateUrl('workingforum_thread',
                [
                    'forum_slug' => $forum_slug,
                    'subforum_slug' => $subforum_slug,
                    'thread_slug' => $thread->getSlug()
                ]
            )); // REDIRECT TO THE NEW THREAD

        }

        $parameters = [ // PARAMETERS USED BY TEMPLATE
            'fileUpload' => $this->getParameter('yosimitso_working_forum.file_upload')
        ];
        $parameters['fileUpload']['maxSize'] = $this->fileUploaderService->getMaxSize();

        return $this->templating->renderResponse('@YosimitsoWorkingForum/Thread/new.html.twig',
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
     *
     * @param $forum_slug
     * @param $subforum_slug
     * @param $thread_slug
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    function resolveAction($forum_slug, $subforum_slug, $thread_slug)
    {

        $thread = $this->em->getRepository(Thread::class)->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new \Exception("Thread error",
                500
            );

        }

        if (!$this->authorization->hasModeratorAuthorization() && $this->user->getId() != $thread->getAuthor()->getId()) // ONLY ADMIN MODERATOR OR THE THREAD'S AUTHOR CAN SET A THREAD AS RESOLVED
        {
            throw new \Exception('You are not authorized to do this', 403);
        }

        $this->threadService->resolve($thread_slug);

        $this->flashbag->add(
            'success',
            $this->translator->trans('message.threadResolved', [], 'YosimitsoWorkingForumBundle')
        );

        return $this->redirect(
            $this->generateUrl('workingforum_thread',
                [
                    'forum_slug' => $forum_slug,
                    'thread_slug' => $thread_slug,
                    'subforum_slug' => $subforum_slug,
                ]
            )
        );
    }

    /**
     * A moderator pin a thread
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @param $forum_slug
     * @param $subforum_slug
     * @param $thread_slug
     * @return RedirectResponse
     * @throws \Exception
     */
    function pinAction($forum_slug, $subforum_slug, $thread_slug)
    {
        $thread = $this->em->getRepository(Thread::class)->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new \Exception("Thread error",
                500
            );

        }

        if ($thread->getPin()) {
            throw new \Exception("Thread already pinned", 500);
        }

        $this->threadService->pin($thread);
        $this->flashbag
            ->add(
                'success',
                $this->translator->trans('message.threadPinned', [], 'YosimitsoWorkingForumBundle')
            );

        return $this->redirect(
            $this->generateUrl('workingforum_thread',
                [
                    'forum_slug' => $forum_slug,
                    'subforum_slug' => $subforum_slug,
                    'thread_slug' => $thread_slug,
                ]
            )
        );
    }

    /**
     * A moderator unpin a thread
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @param $forum_slug
     * @param $subforum_slug
     * @param $thread_slug
     * @return RedirectResponse
     * @throws \Exception
     */
    function unpinAction($forum_slug, $subforum_slug, $thread_slug)
    {
        $thread = $this->em->getRepository(Thread::class)->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new \Exception("Thread error",
                500
            );

        }

        if (!$thread->getPin())
        {
            throw new \Exception("Thread not pinned",500);
        }

        $thread->setPin(false);
        $this->em->persist($thread);
        $this->em->flush();

        $this->flashbag
            ->add(
                'success',
                $this->translator->trans('message.threadUnpinned', [], 'YosimitsoWorkingForumBundle')
            )
        ;

        return $this->redirect(
            $this->generateUrl('workingforum_thread',
                [
                    'forum_slug' => $forum_slug,
                    'subforum_slug' => $subforum_slug,
                    'thread_slug'   => $thread_slug,
                ]
            )
        );
    }

    /**
     * A user report a thread
     * @param $post_id
     * @return Response
     * @throws \Exception
     */
    function reportAction($post_id)
    {
        if (is_null($this->user)) {
            throw new \Exception("user missing error",
                403
            );

        }

        $check_already = $this->em->getRepository(PostReport::class)
            ->findOneBy(['user' => $this->user->getId(), 'post' => $post_id]);

        if (!is_null($check_already)) { // ALREADY WARNED BUT THAT'S OK, THANKS ANYWAY
            return new JsonResponse('true', 200);
        }

        $post = $this->em->getRepository(Post::class)->findOneById($post_id);

        if ($this->threadService->report($post)) {
            return new JsonResponse('true', 200);

        } else {

            return new JsonResponse('false', 200);
        }
    }

    /**
     * The thread is locked by a moderator or admin
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     *
     * @param $forum_slug
     * @param $subforum_slug
     * @param $thread_slug
     *
     * @return RedirectResponse
     */
    function lockAction($forum_slug, $subforum_slug, $thread_slug)
    {
        $thread = $this->em->getRepository(Thread::class)->findOneBySlug($thread_slug);

        if (is_null($thread)) {
            throw new Exception("Thread can't be found", 500);

        }

        $this->threadService->lock($thread);

        $this->flashbag->add(
            'success',
            $this->translator->trans('message.threadLocked', [], 'YosimitsoWorkingForumBundle')
        );

        return $this->redirect(
            $this->generateUrl('workingforum_thread',
                [
                    'forum_slug' => $forum_slug,
                    'thread_slug' => $thread_slug,
                    'subforum_slug' => $subforum_slug,
                ]
            )
        );

    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
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
     * The thread is deleted by modo or admin
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @param $threadslug
     * @return RedirectResponse
     */
    public function deleteThreadAction($thread_slug)
    {
        if (!$this->getParameter('yosimitso_working_forum.allow_moderator_delete_thread')) {
            throw new Exception('Thread deletion is not allowed');
        }

        $thread = $this->em->getRepository(Thread::class)->findOneBySlug($thread_slug);
        $subforum = $this->em->getRepository(Subforum::class)->findOneById($thread->getSubforum()->getId());

        if (is_null($thread)) {
            throw new Exception('Thread cannot be found');
        }
        if (is_null($subforum)) {
            throw new Exception('Thread cannot be found');
        }

        $this->threadService->delete($thread, $subforum);

        $this->flashbag->add(
            'success',
            $this->translator->trans('message.thread_deleted', [], 'YosimitsoWorkingForumBundle')
        );

        return $this->redirect(
            $this->generateUrl('workingforum_subforum',
                [
                    'subforum_slug' => $subforum->getSlug(),
                ]
            )
        );
    }

    /**
     *
     * @param $thread_id
     * @return Response
     */
    public function cancelSubscriptionAction($thread_id)
    {
        if (is_null($this->user)) {
            return new Response(null, 500);
        }
        $thread = $this->em->getRepository(Thread::class)->findOneById($thread_id);
        if (is_null($thread)) {
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
     * @param $thread_id
     * @return Response
     */
    public function addSubscriptionAction($thread_id)
    {
        if (is_null($this->user)) {
            return new Response(null, 500);
        }
        $thread = $this->em->getRepository(Thread::class)->findOneById($thread_id);
        if (is_null($thread)) {
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

        
