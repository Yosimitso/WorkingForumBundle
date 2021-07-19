<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Rules;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Form\RulesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ForumController
 *
 * @Route("/")
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class ForumController extends BaseController
{
    private $dateFormat;
    private $postPerPage;
    private $threadPerPage;

    public function __construct(string $dateFormat, int $postPerPage, int $threadPerPage)
    {
        $this->dateFormat = $dateFormat;
        $this->postPerPage = $postPerPage;
        $this->threadPerPage = $threadPerPage;
    }

    /**
     * Display homepage of forum with subforums
     * @Route("", name="workingforum_forum")
     * @return Response
     */

    public function indexAction()
    {
        $list_forum = $this
            ->em
            ->getRepository(Forum::class)
            ->findAll();

        $this->authorizationGuard->filterForumAccess($list_forum);

        $parameters  = [ // PARAMETERS USED BY TEMPLATE
            'dateFormat' => $this->dateFormat
            ];

        return $this->render(
            '@YosimitsoWorkingForum/Forum/index.html.twig',
            [
                'list_forum' => $list_forum,
                'parameters' => $parameters
            ]
        );
    }

    /**
     * Display the thread list of a subforum
     *
     * @Route("{forum}/{subforum}/view", name="workingforum_subforum")
     * @param Request $request
     * @param int $page
     * @return Response
     */
    public function subforumAction(Forum $forum, Subforum $subforum, Request $request, $page = 1)
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

        return $this->render(
            '@YosimitsoWorkingForum/Forum/thread_list.html.twig',
            [
                'forum' => $forum,
                'subforum' => $subforum,
                'thread_list' => $list_subforum,
                'date_format' => $date_format,
                'forbidden' => false,
                'post_per_page' => $this->postPerPage,
                'page_prefix' => 'page',
                'parameters' => $parameters
            ]
        );
    }

    /**
     * @Route("rules", name="workingforum_rules")
     * @Route("rules/{locale}", name="workingforum_rules", requirements={"locale":"\D+"})
     * @param null|string $locale
     * @return mixed
     */
    public function rulesAction($locale = null)
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
}
