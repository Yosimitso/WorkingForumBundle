<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Controller\BaseController;
/**
 * Class ForumController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class ForumController extends BaseController
{
    /**
     * Display homepage of forum with subforums
     *
     * @return Response
     */

    public function indexAction()
    {
        $list_forum = $this
            ->em
            ->getRepository('Yosimitso\WorkingForumBundle\Entity\Forum')
            ->findAll();

        return $this->render(
            '@YosimitsoWorkingForum/Forum/index.html.twig',
            [
                'list_forum' => $list_forum,
            ]
        );
    }

    /**
     * Display the thread list of a subforum
     *
     * @param         $subforum_slug
     * @param Request $request
     * @param int $page
     *
     * @return Response
     */
    public function subforumAction($subforum_slug, Request $request, $page = 1)
    {
        $subforum = $this
            ->em
            ->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')
            ->findOneBySlug($subforum_slug);
        if (!$this->authorization->hasSubforumAccess($subforum)) {

            return $this->render(
                'YosimitsoWorkingForumBundle:Forum:thread_list.html.twig',
                [
                    'subforum' => $subforum,
                    'forbidden' => true,
                    'forbiddenMsg' => $this->authorization->getErrorMessage()


                    //$this->getParameter('knp_paginator.default_options.page_name')
                ]
            );
        }


        $list_subforum_query = $this
            ->em
            ->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')
            ->findBySubforum(
                $subforum->getId(),
                ['pin' => 'DESC', 'lastReplyDate' => 'DESC']
            );

        $date_format = $this->getParameter('yosimitso_working_forum.date_format');

        $list_subforum = $this->paginator->paginate(
            $list_subforum_query,
            $request->query->get('page', 1)/*page number*/,
            $this->getParameter('yosimitso_working_forum.thread_per_page') /*limit per page*/
        );


        return $this->render(
            '@YosimitsoWorkingForum/Forum/thread_list.html.twig',
            [
                'subforum' => $subforum,
                'thread_list' => $list_subforum,
                'date_format' => $date_format,
                'forbidden' => false,
                'post_per_page' => $this->getParameter('yosimitso_working_forum.post_per_page'),
                'page_prefix' => 'page'
                //$this->getParameter('knp_paginator.default_options.page_name')
            ]
        );
    }
}
