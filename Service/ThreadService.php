<?php

namespace Yosimitso\WorkingForumBundle\Service;

use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Form\ThreadType;
use Yosimitso\WorkingForumBundle\Util\FileUploader;
use Yosimitso\WorkingForumBundle\Util\Slugify;

class ThreadService
{
    private $lockThreadOlderThan;
    private $paginator;
    private $postPerPage;
    private $requestStack;
    protected $em;
    protected $user;
    protected $fileUploadUtil;

    public function __construct($lockThreadOlderThan, $paginator, $postPerPage, $requestStack, $em, $user, FileUploader $fileUploadUtil)
    {
        $this->lockThreadOlderThan = $lockThreadOlderThan;
        $this->paginator = $paginator;
        $this->postPerPage = $postPerPage;
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->user = $user;
        $this->fileUploadUtil = $fileUploadUtil;

    }

    /**
     * @param $thread
     * @return bool
     * @throws \Exception
     * Is the thread autolocked ?
     */
    public function isAutolock($thread)
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
     * Generates a slug for a thread
     */
    public function slugify(Thread $thread)
    {
        return $thread->getId().'-'.Slugify::convert($thread->getLabel());
    }

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
     * @param ThreadType $form
     * @param Post $post
     * @param Thread $thread
     * @param Subforum $subforum
     * @return bool
     * @throws \Exception
     * Create a thread
     */
    public function create(ThreadType $form, Post $post, Thread $thread, Subforum $subforum)
    {
        $subforum->newThread($this->user); // UPDATE STATISTIC

        $this->user->addNbPost(1);
        $this->em->persist($this->user);

        $post->setThread($thread); // ATTACH TO THREAD
        $this->em->persist($thread);
        $this->em->persist($subforum);
        $this->em->flush(); // GET THREAD ID

        $thread->setSlug($this->slugify($thread)); // SLUG NEEDS THE ID
        $this->em->persist($thread);

        if (!empty($form->getData()->getPost()[0]->getFilesUploaded())) {
            $file = $this->fileUploadUtil->upload($form->getData()->getPost()[0]->getFilesUploaded(), $post);
            if (!$file) { // FILE UPLOAD FAILED
                throw new \Exception($this->fileUploadUtil->getErrorMessage());
            }

            $post->addFiles($file);
        }

        $this->em->persist($post);
        $this->em->flush();

        return true;
    }




}