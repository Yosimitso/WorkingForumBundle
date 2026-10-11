<?php

namespace Yosimitso\WorkingForumBundle\Controller\Api;

use Doctrine\ORM\Mapping\Driver\AttributeReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\Rules;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Form\RulesType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Yosimitso\WorkingForumBundle\Service\BundleParametersService;
use Yosimitso\WorkingForumBundle\Util\ApiHelper;
use Yosimitso\WorkingForumBundle\Controller\BaseController;

class PostController extends BaseController
{
    /**
     * A user report a post
     * @throws \Exception
     */
    #[Route('api/{forum}/{subforum}/report/{post}', name: 'workingforum_api_report_post', requirements: ['post_id' => '\d+'])]
    public function reportAction(Post $post): Response
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
}
