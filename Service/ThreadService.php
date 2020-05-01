<?php

namespace Yosimitso\WorkingForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Paginator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;
use Yosimitso\WorkingForumBundle\Form\MoveThreadType;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Form\ThreadType;
use Yosimitso\WorkingForumBundle\Security\Authorization;
use Yosimitso\WorkingForumBundle\Service\FileUploaderService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Yosimitso\WorkingForumBundle\Util\Slugify;
use Symfony\Component\Form\FormFactory;


class ThreadService
{
    /**
     * @var int
     */
    protected $lockThreadOlderThan;
    /**
     * @var Paginator
     */
    protected $paginator;
    /**
     * @var int
     */
    protected $postPerPage;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    protected $em;
    /**
     * @var UserInterface
     */
    protected $user;
    /**
     * @var FileUploaderService
     */
    protected $fileUploaderService;
    /**
     * @var Authorization
     */
    protected $authorization;
    /**
     * @var BundleParametersService
     */
    protected $bundleParameters;
    /**
     * @var FormFactory
     */
    protected $formFactory;

    protected $router;

    public function __construct(
        $lockThreadOlderThan,
        Paginator $paginator,
        $postPerPage,
        RequestStack $requestStack,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        FileUploaderService $fileUploaderService,
        Authorization $authorization,
        BundleParametersService $bundleParameters,
        FormFactory $formFactory,
        RouterInterface $router
    )
    {
        $this->lockThreadOlderThan = $lockThreadOlderThan;
        $this->paginator = $paginator;
        $this->postPerPage = $postPerPage;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->fileUploaderService = $fileUploaderService;
        $this->authorization = $authorization;
        $this->bundleParameters = $bundleParameters;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param Thread $thread
     * @return bool
     * @throws \Exception
     *
     * Is the thread autolocked ?
     */
    public function isAutolock(Thread $thread)
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
     * @param $postQuery
     * @return mixed
     *
     * Return the post list according to pagination parameters and query
     */
    public function paginate($postQuery)
    {
        return $this->paginator->paginate(
            $postQuery,
            $this->requestStack->getCurrentRequest()->query->get('page', 1),
            $this->postPerPage
        );
    }

    /**
     * @param Thread $thread
     * @return string
     *
     * Generates a slug for a thread
     */
    public function slugify(Thread $thread)
    {
        return $thread->getId() . '-' . Slugify::convert($thread->getLabel());
    }

    /**
     * @param Thread $thread
     * @return bool
     *
     * Pin a thread
     */
    public function pin(Thread $thread)
    {
        $thread->setPin(true);

        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * @param Thread $thread
     * @return bool
     *
     * Resolve thread
     */
    public function resolve(Thread $thread)
    {
        $thread->setResolved(true);
        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * @param Thread $thread
     * @return bool
     *
     * Lock thread
     */
    public function lock(Thread $thread)
    {
        $thread->setLocked(true);
        $this->em->persist($thread);
        $this->em->flush();

        return true;
    }

    /**
     * @param Post $post
     * @return bool
     *
     * Report a thread
     */
    public function report(Post $post)
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
     * @param Thread $thread
     * @param Subforum $currentSubforum
     * @param Subforum $targetSubforum
     * @return bool
     *
     * Move thread to an another subforum
     */
    public function move(Thread $thread, Subforum $currentSubforum, Subforum $targetSubforum)
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
     * @param Thread $thread
     * @param Subforum $subforum
     * @return bool
     *
     * Delete a thread
     */
    public function delete(Thread $thread, Subforum $subforum)
    {
        $subforum->addNbThread(-1);
        $subforum->addNbPost(-$thread->getnbReplies());

        $this->em->persist($subforum);
        $this->em->remove($thread);
        $this->em->flush();

        return true;
    }

    /**
     * @param Form $form
     * @param Post $post
     * @param Thread $thread
     * @param Subforum $subforum
     * @return bool
     * @throws \Exception
     *
     * Create a thread
     */
    public function create(Form $form, Post $post, Thread $thread, Subforum $subforum)
    {

        $this->em->beginTransaction();
        try {


            $subforum->newThread($this->user); // UPDATE STATISTIC

            $this->user->addNbPost(1);
            $this->em->persist($this->user);

            $this->em->persist($thread);
            $this->em->persist($subforum);
            $this->em->flush(); // GET THREAD ID

            $thread->setSlug($this->slugify($thread)); // SLUG NEEDS THE ID
            $post->setThread($thread); // ATTACH TO THREAD
            $this->em->persist($thread);

            if (!empty($form->getData()->getPost()[0]->getFilesUploaded())) {
                $file = $this->fileUploaderService->upload($form->getData()->getPost()[0]->getFilesUploaded(), $post);
                $post->addFiles($file);
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
     * @param Subforum $subforum
     * @param Thread $thread
     * @param Post $post
     * @param UserInterface $user
     * @param PostType $form
     * @return bool
     * @throws \Exception
     *
     * Create a post
     */
    public function post(Subforum $subforum, Thread $thread, Post $post, UserInterface $user, $form)
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
            $post->addFiles($file);
        }

        $this->em->persist($post);
        $this->em->flush();

        return true;
    }

    /**
     * @param UserInterface $user
     * @param Thread $thread
     * @param bool $autolock
     * @param bool $canSubscribeThread
     * @return array
     *
     * Get available actions for a given user
     */
    public function getAvailableActions(?UserInterface $user, Thread $thread, $autolock, $canSubscribeThread)
    {
        $anonymousUser = (is_null($user)) ? true : false;

        return [
            'setResolved' => $this->authorization->hasModeratorAuthorization() || (!$anonymousUser && $user->getId() == $thread->getAuthor()->getId()),
            'quote' => (!$anonymousUser && !$thread->getLocked()),
            'report' => (!$anonymousUser),
            'post' => ((!$anonymousUser && !$autolock) || $this->authorization->hasModeratorAuthorization()),
            'subscribe' => (!$anonymousUser && $canSubscribeThread),
            'moveThread' => ($this->authorization->hasModeratorAuthorization()) ? $this->formFactory->create(MoveThreadType::class)->createView() : false,
            'asModerator' => $this->authorization->hasModeratorAuthorization(),
            'asAdmin' => $this->authorization->hasAdminAuthorization(),
            'allowModeratorDeleteThread' => $this->bundleParameters->allow_moderator_delete_thread
        ];
    }

    /**
     * @param Forum $forum
     * @param Subforum $subforum
     * @param Thread $thread
     * @param int $page
     * @return RedirectResponse
     */
    public function redirectToThread(Forum $forum, Subforum $subforum, Thread $thread, $page = 1)
    {
        return new RedirectResponse($this->router->generate('workingforum_thread',
            [
                'forum_slug' => $forum->getSlug(),
                'subforum_slug' => $subforum->getSlug(),
                'thread_slug' => $thread->getSlug(),
                'page' => $page
            ]
        ));
    }

    /**
     * @param Forum $forum
     * @param Subforum $subforum
     * @return RedirectResponse
     */
    public function redirectToSubforum(Forum $forum, Subforum $subforum)
    {
        return new RedirectResponse($this->router->generate('workingforum_subforum',
            [
                'forum_slug' => $forum->getSlug(),
                'subforum_slug' => $subforum->getSlug(),
            ]
        ));
    }

}
