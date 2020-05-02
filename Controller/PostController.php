<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostVote;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Service\ThreadService;

/**
 * Class PostController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class PostController extends BaseController
{
    /**
     * @var ThreadService 
     */
    protected $threadService;

    public function __construct(ThreadService $threadService)
    {
         $this->threadService = $threadService;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     * vote for a post
     */
    public function voteUpAction(Request $request)
    {
        $postId = $request->get('postId');

        if (is_null($this->user)) {
            return new JsonResponse(['res' => 'false', 'errMsg' => 'You must be a registered user'], 403);
        }

        $post = $this->em->getRepository(Post::class)->findOneById($postId);
        $subforum = $this->em->getRepository(Subforum::class)->findOneById(
            $post->getThread()->getSubforum()->getId()
        );

        if (is_null($subforum)) {
            return new JsonResponse(['res' => 'false', 'errMsg' => 'Internal error'], 500);
        }

        if (!$this->authorization->hasSubforumAccess(
            $subforum
        )) { // CHECK IF USER HAS AUTHORIZATION TO VIEW THIS THREAD
            return new JsonResponse(['res' => 'false'], 403);
        }

        if (is_null($post)) {
            return new JsonResponse(['res' => 'false', 'errMsg' => 'Thread not found'], 500);
        }
        if ($post->getUser()->getId() == $this->user->getId()) { // CAN'T VOTE FOR YOURSELF
            return new JsonResponse(['res' => 'false', 'errMsg' => 'An user can\'t vote for his post'], 403);
        }
        if (!empty($post->getModerateReason()) || $post->getThread()->getLocked() || $this->threadService->isAutolock($post->getThread()) ) {
            return new JsonResponse(['res' => 'false', 'errMsg' => 'You can\'t vote for this post'], 403);
        }


        $alreadyVoted = $this->em->getRepository(PostVote::class)->findOneBy(
            ['user' => $this->user, 'post' => $post]
        );

        if (is_null($alreadyVoted)) {
            $postVote = new PostVote();
            $postVote->setPost($post)
                ->setUser($this->user)
                ->setVoteType(PostVote::VOTE_UP)
                ->setThread($post->getThread());

            $post->addVoteUp();

            $this->em->persist($postVote);
            $this->em->persist($post);
            $this->em->flush();

            return new JsonResponse(['res' => 'true', 'voteUp' => $post->getVoteUp()], 200);
        } else {
            return new JsonResponse(['res' => 'false', 'errMsg' => 'Already voted'], 403);
        }
    }

}
