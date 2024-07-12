<?php

namespace Yosimitso\WorkingForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;
use Yosimitso\WorkingForumBundle\Form\MoveThreadType;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Form\ThreadType;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuardInterface;
use Yosimitso\WorkingForumBundle\Service\FileUploaderService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Yosimitso\WorkingForumBundle\Util\Slugify;
use Symfony\Component\Form\FormFactory;


class ThreadService
{
    protected ?UserInterface $user;

    public function __construct(
        protected readonly int $lockThreadOlderThan,
        protected readonly PaginatorInterface $paginator,
        protected readonly int $postPerPage,
        protected readonly RequestStack $requestStack,
        protected readonly EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        protected readonly FileUploaderService $fileUploaderService,
        protected readonly AuthorizationGuardInterface $authorizationGuard,
        protected readonly BundleParametersService $bundleParameters,
        protected readonly FormFactoryInterface $formFactory,
        protected readonly RouterInterface $router,
        protected readonly Environment $templating
    )
    {
        $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;
        $this->user = is_object($user) ? $user : null;
    }

    /**
     * @throws \Exception
     *
     * Is the thread autolocked ?
     */
    public function isAutolock(Thread $thread) : bool
    {
        if ($this->lockThreadOlderThan) {
            $diff = $thread->getLastReplyDate()->diff(new \DateTime());
            if ($diff->days > $this->lockThreadOlderThan) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * @param array<mixed> $postQuery
     */
    public function paginate(array $postQuery) : PaginationInterface
    {
        return $this->paginator->paginate(
            $postQuery,
            $this->requestStack->getCurrentRequest()->query->get('page', 1),
            $this->postPerPage
        );
    }

    /**
     * Generates a slug for a thread
     */
    public function slugify(Thread $thread) : string
    {
        return $thread->getId() . '-' . Slugify::convert($thread->getLabel());
    }

    /**
     * Pin a thread
     */
    public function pin(Thread $thread) : bool
    {
        $thread->setPin(true);

        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * Resolve thread
     */
    public function resolve(Thread $thread) : bool
    {
        $thread->setResolved(true);
        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * Lock thread
     */
    public function lock(Thread $thread) : bool
    {
        $thread->setLocked(true);
        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * Report a thread
     */
    public function report(Post $post) : bool
    {
        if (!is_null($post) && empty($post->getModerateReason()) && !is_null($this->user)) // THE POST EXISTS AND IS "VISIBLE"
        {
            $report = new PostReport;
            $report->setPost($post)
                    ->setUser($this->user);
            $this->em->persist($report);
            $this->em->flush();

            return true;
        } else {

            return false;
        }
    }

    /**
     * Move thread to an another subforum
     */
    public function move(Thread $thread, Subforum $currentSubforum, Subforum $targetSubforum) : bool
    {
        $currentSubforum->setNbThread($currentSubforum->getNbThread() - 1);
        $currentSubforum->setNbPost($currentSubforum->getNbPost() - $thread->getNbReplies());

        $thread->setSubforum($targetSubforum);

        $targetSubforum->setNbThread($targetSubforum->getNbThread() + 1);
        $targetSubforum->setNbPost($targetSubforum->getNbPost() + $thread->getNbReplies());

        $this->em->persist($thread);
        $this->em->persist($currentSubforum);
        $this->em->persist($targetSubforum);
        $this->em->flush();

        return true;
    }

    /**
     * Delete a thread
     */
    public function delete(Thread $thread, Subforum $subforum) : bool
    {
        $subforum->addNbThread(-1);
        $subforum->addNbPost(-$thread->getnbReplies());

        $this->em->persist($subforum);
        $this->em->remove($thread);
        $this->em->flush();

        return true;
    }

    /**
     * @throws \Exception
     *
     * Create a thread
     */
    public function create($form, Post $post, Thread $thread, Subforum $subforum) : bool
    {

        $this->em->beginTransaction();
        try {
            $subforum->newThread($this->user); // UPDATE STATISTIC

            $this->user->addNbPost(1);
            $this->em->persist($this->user);

            $post->setThread($thread); // ATTACH TO THREAD
            $this->em->persist($thread);
            $this->em->persist($subforum);
            $this->em->flush(); // GET THREAD ID

            $thread->setSlug($this->slugify($thread)); // SLUG NEEDS THE ID

            $this->em->persist($thread);
            $this->em->flush();

            if (!empty($form->getData()->getPost()[0]->getFilesUploaded())) {
                $file = $this->fileUploaderService->upload($form->getData()->getPost()[0]->getFilesUploaded(), $post);
                $post->addFile($file);
            }

            $this->em->persist($post);
            $this->em->flush();

            $this->em->commit();

            return true;
        } catch (\Exception $e) {
            $this->em->rollback();

            throw $e;
        }
    }

    /**
     * Create a post
     */
    public function post(Subforum $subforum, Thread $thread, Post $post, UserInterface $user, $form) : bool
    {
        $subforum->newPost($user); // UPDATE SUBFORUM STATISTIC
        $thread->addReply($user); // UPDATE THREAD STATISTIC

        $user->addNbPost(1);

        $this->em->persist($user);
        $this->em->persist($thread);
        $this->em->persist($post); // COULD FAILED IF EVENTS THROW EXCEPTIONS
        $this->em->persist($subforum);

        if (!empty($form->getData()->getFilesUploaded())) {
            $file = $this->fileUploaderService->upload($form->getData()->getFilesUploaded(), $post);
            $post->addFile($file);
        }

        $this->em->persist($post);
        $this->em->flush();

        return true;
    }

    /**
     * Get available actions for a given user
     */
    public function getAvailableActions(?UserInterface $user, Thread $thread, $autolock, $canSubscribeThread)
    {
        $anonymousUser = (!$user instanceof UserInterface);

        return [
            'setResolved' => $this->authorizationGuard->hasModeratorAuthorization() || (!$anonymousUser && $user->getId() == $thread->getAuthor()->getId()),
            'quote' => (!$anonymousUser && !$thread->getLocked()),
            'report' => (!$anonymousUser),
            'post' => ((!$anonymousUser && !$autolock) || $this->authorizationGuard->hasModeratorAuthorization()),
            'subscribe' => (!$anonymousUser && $canSubscribeThread),
            'moveThread' => ($this->authorizationGuard->hasModeratorAuthorization()) ? $this->formFactory->create(MoveThreadType::class)->createView() : false,
            'asModerator' => $this->authorizationGuard->hasModeratorAuthorization(),
            'asAdmin' => $this->authorizationGuard->hasAdminAuthorization(),
            'allowModeratorDeleteThread' => $this->bundleParameters->allow_moderator_delete_thread
        ];
    }


    public function redirectToThread(Forum $forum, Subforum $subforum, Thread $thread, $page = 1) : RedirectResponse
    {
        return new RedirectResponse($this->router->generate('workingforum_thread',
            [
                'forum' => $forum->getSlug(),
                'subforum' => $subforum->getSlug(),
                'thread' => $thread->getSlug(),
                'page' => $page
            ]
        ));
    }

    public function redirectToSubforum(Forum $forum, Subforum $subforum): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('workingforum_subforum',
            [
                'forum' => $forum->getSlug(),
                'subforum' => $subforum->getSlug(),
            ]
        ));
    }

    public function redirectToForbiddenAccess(Subforum $subforum, $forbiddenMessage): Response
    {
            return $this->templating->render(
                '@YosimitsoWorkingForum/Forum/thread_list.html.twig',
                [
                    'subforum' => $subforum,
                    'forbidden' => true,
                    'forbiddenMsg' => $forbiddenMessage
                ]
            );
    }
}
