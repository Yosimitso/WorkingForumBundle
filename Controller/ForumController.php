<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Doctrine\ORM\Mapping\Driver\AttributeReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Yosimitso\WorkingForumBundle\Entity\Forum;
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
use Yosimitso\WorkingForumBundle\Util\ApiHelper;

#[Route('/')]
class ForumController extends BaseController
{
    public function __construct(
        protected readonly string $dateFormat,
        protected readonly int $postPerPage,
        protected readonly int $threadPerPage
    ) {}

    /**
     * Display homepage of forum with subforums
     */
    #[Route('{isApi}', name: 'workingforum_forum', requirements: ['isApi' => '(api\/)?'])]
    public function indexAction(bool $isApi = false): Response
    {
        $list_forum = $this
            ->em
            ->getRepository(Forum::class)
            ->findAll();

        $this->authorizationGuard->filterForumAccess($list_forum);

        $parameters  = [ // PARAMETERS USED BY TEMPLATE
            'dateFormat' => $this->dateFormat
            ];

        $templateParameters =
        [
            'list_forum' => $list_forum,
            'parameters' => $parameters
        ];

        return $isApi
            ? new JsonResponse(ApiHelper::getSerializer()->serialize($templateParameters, 'json'), 200, ['groups' => 'threadList'], true)
            : $this->render('@YosimitsoWorkingForum/Forum/index.html.twig', $templateParameters);
    }

    /**
     * Display the thread list of a subforum
     */
    #[Route('{isApi}{forum}/{subforum}/view', name: 'workingforum_subforum', requirements: ['isApi' => '(api\/)?'])]
    public function subforumAction(Forum $forum, Subforum $subforum, Request $request, bool $isApi = false): Response
    {
        $list_subforum_query = $this
            ->em
            ->getRepository(Thread::class)
            ->getAllBySubforum(
                $subforum
            );

        $date_format = $this->dateFormat;

        $list_subforum = $this->paginator->paginate(
            $list_subforum_query,
            $request->query->get('page', 1)/*page number*/,
            $this->threadPerPage /*limit per page*/
        );

        $parameters  = [ // PARAMETERS USED BY TEMPLATE
            'dateFormat' => $this->dateFormat
        ];
        
        $templateParameters =
        [
            'forum' => $forum,
            'subforum' => $subforum,
            'thread_list' => $list_subforum,
            'date_format' => $date_format,
            'forbidden' => false,
            'post_per_page' => $this->postPerPage,
            'page_prefix' => 'page',
            'parameters' => $parameters
        ];

        return $isApi
                ? new JsonResponse(ApiHelper::getSerializer()->serialize($templateParameters, 'json'), 200, ['groups' => 'threadList'], true)
                :$this->render('@YosimitsoWorkingForum/Forum/thread_list.html.twig', $templateParameters)
            ;
    }

    #[Route('rules', name: 'workingforum_rules')]
    #[Route('rules/{locale}', name: 'workingforum_rules', requirements: ['locale' => '\D+'])]
    public function rulesAction(?string $locale = null): Response
    {
        if (is_null($locale)) {
            $rulesList = $this->em->getRepository(Rules::class)->findAll();

            if (!empty($rulesList)) {
                $rules = $rulesList[0];
            } else {
                $rules = null;
            }
        } else {
            $rules = $this->em->getRepository(Rules::class)->findOneByLang($locale);
        }

        $form = $this->createForm(RulesType::class, null);
        
        return $this->render(
            '@YosimitsoWorkingForum/Forum/rules.html.twig',
            [
                'rules' => $rules,
                'form' => $form->createView()
            ]
        );
    }

    #[Route('test', name: 'workingforum_test')]
    public function testAction(): Response
    {
        return $this->render(
            '@YosimitsoWorkingForum/Vue/test.html.twig',
            [
            ]
        );
    }
}
