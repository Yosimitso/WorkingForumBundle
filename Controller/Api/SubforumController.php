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

class SubforumController extends BaseController
{
    #[Route('api/{forum}/{subforum}', name: 'workingforum_api_subforum')]
    public function subforumInfoApiAction(Subforum $subforum): Response
    {
        return new JsonResponse(ApiHelper::getSerializer()->serialize($subforum, 'json'), 200, ['groups' => 'subforum'], true);
    }

    #[Route('api/{forum}/{subforum}/view/{page}', name: 'workingforum_api_subforum', requirements: ['page' => '\d+'])]
    public function subforumApiAction(Forum $forum, Subforum $subforum, int $page): Response
    {
        $list_subforum = $this
            ->em
            ->getRepository(Thread::class)
            ->getAllBySubforum(
                $subforum,
                $page

            );

        $templateParameters =
            [
                'forum' => $forum,
                'subforum' => $subforum,
                'thread_list' => $list_subforum,
                'nb_items' => $this->em->getRepository(Thread::class)->countAllBySubforum($subforum),
                'forbidden' => false,
                'page_prefix' => 'page',
            ];

        return new JsonResponse(ApiHelper::getSerializer()->serialize($templateParameters, 'json'), 200, ['groups' => 'threadList'], true)
            ;
    }
}
