<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Form\SearchType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class SearchController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class SearchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $listForum = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Forum')->findAll();
        $form = $this->get('form.factory')
            ->createNamedBuilder('', SearchType::class, null, array('csrf_protection' => false,))
            ->add('page', HiddenType::class, ['data' => 1])
            ->setMethod('GET')
            ->getForm()
        ;
        $form->handleRequest($request);
        $authorizationChecker = $this->get('yosimitso_workingforum_authorization');

        if ($form->isSubmitted()) {
            if ($form->isValid())
            {
                $whereSubforum = (array) $authorizationChecker->hasSubforumAccessList($form['forum']->getData());

                $thread_list_query = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')
                                        ->search($form['keywords']->getData(), 0, 100, $whereSubforum)
                ;
                $date_format = $this->container->getParameter('yosimitso_working_forum.date_format');

                $paginator = $this->get('knp_paginator');

                if (!is_null($thread_list_query)) {
                    $thread_list = $paginator->paginate(
                        $thread_list_query,
                        $request->query->get('page', 1)/*page number*/,
                        $this->container->getParameter('yosimitso_working_forum.thread_per_page')
                    ); /*limit per page*/
                }
                else
                {
                    $thread_list = [];
                }

                return $this->render('YosimitsoWorkingForumBundle:Forum:thread_list.html.twig',
                    [
                        'thread_list' => $thread_list,
                        'date_format' => $date_format,
                        'keywords'    => $form['keywords']->getData(),
                        'post_per_page' => $this->getParameter('yosimitso_working_forum.post_per_page'),
                        'page_prefix'   => 'page'
                    ]
                );
            }
        }

        return $this->render('YosimitsoWorkingForumBundle:Search:search.html.twig',
            [
                'listForum' => $listForum,
                'form'      => $form->createView(),
            ]
        );
    }
}