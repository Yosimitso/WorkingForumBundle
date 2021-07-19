<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
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
     * @var FormFactory 
     */
    protected $formFactory;
    
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $listForum = $this->em->getRepository(Forum::class)->findAll();
        $form = $this->formFactory
            ->createNamedBuilder('', SearchType::class, null, array('csrf_protection' => false,))
            ->add('page', HiddenType::class, ['data' => 1])
            ->setMethod('GET')
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid())
            {
                $whereSubforum = (array) $this->authorizationGuard->hasSubforumAccessList($form['forum']->getData()->toArray());

                $thread_list_query = $this->em->getRepository(Thread::class)
                                        ->search($form['keywords']->getData(), 0, 100, $whereSubforum)
                ;
                $date_format = $this->bundleParameters->date_format;

                if (!is_null($thread_list_query)) {
                    $thread_list = $this->paginator->paginate(
                        $thread_list_query,
                        $request->query->get('page', 1)/*page number*/,
                        $this->bundleParameters->thread_per_page
                    ); /*limit per page*/
                }
                else
                {
                    $thread_list = [];
                }

                $parameters  = [ // PARAMETERS USED BY TEMPLATE
                    'dateFormat' => $this->bundleParameters->date_format
                ];

                return $this->render('@YosimitsoWorkingForum/Forum/thread_list.html.twig',
                    [
                        'thread_list' => $thread_list,
                        'date_format' => $date_format,
                        'keywords'    => $form['keywords']->getData(),
                        'post_per_page' => $this->bundleParameters->post_per_page,
                        'page_prefix'   => 'page',
                        'parameters' => $parameters
                    ]
                );
            }
        }

        return $this->render('@YosimitsoWorkingForum/Search/search.html.twig',
            [
                'listForum' => $listForum,
                'form'      => $form->createView(),
            ]
        );
    }
}
