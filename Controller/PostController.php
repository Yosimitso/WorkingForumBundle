<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Entity\PostVote;

/**
 * Class ThreadController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class PostController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     * vote for a post
     */
    public function voteUpAction(Request $request)
    {
        $postId = $request->get('postId');

        $post = $this->em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById($postId);
        if (is_null($this->user)) {
            return new Response(json_encode(['res' => 'false', 'errMsg' => 'You must be a registered user'], 403));
        }
        if (is_null($post)) {
            return new Response(json_encode(['res' => 'false', 'errMsg' => 'Thread not found'], 500));
        }
        if ($post->getUser()->getId() == $this->user->getId()) { // CAN'T VOTE FOR YOURSELF
            return new Response(json_encode(['res' => 'false', 'errMsg' => 'An user can\'t vote for his post'], 403));
        }
        if (!empty($post->getModerateReason()) || $post->getThread()->getLocked() || $this->get('yosimitso_workingforum_util_thread')->isAutolock($post->getThread()) ) {
            return new Response(json_encode(['res' => 'false', 'errMsg' => 'You can\'t vote for this post'], 403));
        }

        $subforum = $this->em->getRepository('YosimitsoWorkingForumBundle:Subforum')->findOneById(
            $post->getThread()->getSubforum()->getId()
        );

        if (is_null($subforum)) {
            return new Response(json_encode(['res' => 'false', 'errMsg' => 'Internal error'], 500));
        }

        if (!$this->authorization->hasSubforumAccess(
            $subforum
        )) { // CHECK IF USER HAS AUTHORIZATION TO VIEW THIS THREAD
            return new Response(json_encode(['res' => 'false'], 403));
        }

        $alreadyVoted = $this->em->getRepository('YosimitsoWorkingForumBundle:PostVote')->findOneBy(
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

            return new Response(json_encode(['res' => 'true', 'voteUp' => $post->getVoteUp()], 200));
        } else {
            return new Response(json_encode(['res' => 'false', 'errMsg' => 'Already voted'], 403));
        }
    }

}