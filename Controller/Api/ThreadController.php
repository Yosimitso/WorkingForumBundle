<?php

namespace Yosimitso\WorkingForumBundle\Controller\Api;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostVote;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Subscription;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Service\FileUploaderService;
use Yosimitso\WorkingForumBundle\Service\ThreadService;
use Yosimitso\WorkingForumBundle\Twig\Extension\SmileyTwigExtension;
use Yosimitso\WorkingForumBundle\Util\ApiHelper;

class ThreadController extends BaseController
{
    public function __construct(
        protected readonly FileUploaderService $fileUploaderService,
        protected readonly SmileyTwigExtension $smileyTwigExtension,
        protected readonly ThreadService $threadService,
    )
    {
        
    }
    #[Route('api/{forum}/{subforum}/{thread}/view', name: 'workingforum_api_thread_view')]
    public function indexApiAction(Forum $forum, Subforum $subforum, Thread $thread, Request $request): Response
    {
        $autolock = $this->threadService->isAutolock($thread); // CHECK IF THREAD IS AUTOMATICALLY LOCKED (TOO OLD?)
        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown

        $canSubscribeThread = $this->bundleParameters->thread_subscription['enable']; // SUBSCRIPTION SYSTEM DISABLED

        if (!$this->isUserAnonymous()) {
            $post = new Post($this->user, $thread);
            $form = $this->createForm(PostType::class, $post, ['canSubscribeThread' => $canSubscribeThread]); // create form for posting
            $form->handleRequest($request);

            if ($form->isSubmitted()) { // USER SUBMIT HIS POST

                if ($this->user->isBanned()) // USER IS BANNED CAN'T POST
                {
                    return new JsonResponse(['error' => 'message.banned', Response::HTTP_BAD_REQUEST]);
                }

                if ($autolock && !$this->authorizationGuard->hasModeratorAuthorization()) // THREAD IS LOCKED CAUSE TOO OLD ACCORDING TO PARAMETERS
                {
                    return new JsonResponse(['error' => 'thread_too_old_locked', Response::HTTP_BAD_REQUEST]);
                }

                if ($form->isValid()) {

                    try {
                        $this->threadService->post($subforum, $thread, $post, $this->user, $form);
                        return new JsonResponse(status: Response::HTTP_CREATED);
                    } catch (\Exception $e) {
                        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    $errors = [];
                    /** @var FormError $error */
                    foreach ($form->getErrors() as $error) {
                        $errors[] = $error->getMessage();
                    }
                    return new JsonResponse(['error' => $errors], Response::HTTP_BAD_REQUEST);
                }
            }
        }

        $postQuery = $this->em
            ->getRepository(Post::class)
            ->findByThread($thread->getId());
        $post_list = $this->threadService->paginate($postQuery);

        $hasAlreadyVoted = $this->em->getRepository(PostVote::class)->getThreadVoteByUser($thread, $this->user);


        $actionsAvailable = $this->threadService->getAvailableActionApi($this->user, $thread, $autolock, $canSubscribeThread);
        $subscripted = $this->em->getRepository(Subscription::class)->findOneBy(['thread' => $thread, 'user' => $this->user]);

        $templateParameters =
            [
                'forum' => $forum,
                'subforum' => $subforum,
                'thread' => $thread,
                'post_list' => $post_list,
                'listSmiley' => $listSmiley,
                'forbidden' => false,
                //'request' => $request,
                'autolock' => $autolock,
                'hasAlreadyVoted' => $hasAlreadyVoted,
                'actionsAvailable' => $actionsAvailable,
                'hasSubscribed' => (is_null($subscripted)) ? false : true,
//            'token' => $form->createView()->children['_token']->vars['value']
        ];

        return new JsonResponse(ApiHelper::getSerializer()->serialize($templateParameters, 'json'), 200, ['groups' => 'threadList'], true);

    }
    
    /**
     * The thread is resolved
     * @throws \Exception
     */
    #[Route('api/{forum}/{subforum}/{thread}/resolved', name: 'workingforum_api_resolve_thread')]
    public function resolveAction(Forum $forum, Subforum $subforum, Thread $thread): Response
    {
        if (!$this->authorizationGuard->hasModeratorAuthorization() && $this->user->getId() != $thread->getAuthor()->getId()) // ONLY ADMIN MODERATOR OR THE THREAD'S AUTHOR CAN SET A THREAD AS RESOLVED
        {
            throw new AccessDeniedHttpException('You are not authorized to do this');
        }

        try {
            $this->threadService->resolve($thread);
            return new Response(status: Response::HTTP_OK);
        } catch (\Throwable $e) {
            return new Response(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * An user wants to subscribe to a thread
     */
    #[Route('api/{forum}/{subforum}/addsubscription/{thread}', name: 'workingforum_api_add_subscription')]
    public function addSubscriptionApiAction(Thread $thread): Response
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
    
    /**
     * An user wants to cancel a subscription to a thread
     */
    #[Route('api/{forum}/{subforum}/cancelsubscription/{thread}', name: 'workingforum_api_cancel_subscription')]
    public function cancelSubscriptionApiAction(Thread $thread): Response
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
}
