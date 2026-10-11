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

class ForumController extends BaseController
{
    /**
     * Display homepage of forum with subforums
     */
    #[Route('api/', name: 'workingforum_api_forum')]
    public function indexAction(): Response
    {
        $list_forum = $this
            ->em
            ->getRepository(Forum::class)
            ->findAll();

        $this->authorizationGuard->filterForumAccess($list_forum);

        $templateParameters =
            [
                'list_forum' => $list_forum,
            ];

        return new JsonResponse(ApiHelper::getSerializer()->serialize($templateParameters, 'json'), 200, ['groups' => 'threadList'], true);

    }
}
