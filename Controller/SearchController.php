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
class SearchController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $listForum = $this->em->getRepository('YosimitsoWorkingForumBundle:Forum')->findAll();
        $form = $this->get('form.factory')
            ->createNamedBuilder('', SearchType::class, null, array('csrf_protection' => false,))
            ->add('page', HiddenType::class, ['data' => 1])
            ->setMethod('GET')
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid())
            {
                $whereSubforum = (array) $this->authorization->hasSubforumAccessList($form['forum']->getData());

                $thread_list_query = $this->em->getRepository('YosimitsoWorkingForumBundle:Thread')
                                        ->search($form['keywords']->getData(), 0, 100, $whereSubforum)
                ;
                $date_format = $this->getParameter('yosimitso_working_forum.date_format');

                if (!is_null($thread_list_query)) {
                    $thread_list = $this->paginator->paginate(
                        $thread_list_query,
                        $request->query->get('page', 1)/*page number*/,
                        $this->container->getParameter('yosimitso_working_forum.thread_per_page')
                    ); /*limit per page*/
                }
                else
                {
                    $thread_list = [];
                }

                return $this->templating->renderResponse('@YosimitsoWorkingForum/Forum/thread_list.html.twig',
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

        return $this->templating->renderResponse('@YosimitsoWorkingForum/Search/search.html.twig',
            [
                'listForum' => $listForum,
                'form'      => $form->createView(),
            ]
        );
    }
}