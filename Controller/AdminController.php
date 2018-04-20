<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yosimitso\WorkingForumBundle\Entity\Rules;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Form\RulesType;
use Yosimitso\WorkingForumBundle\Twig\Extension\SmileyTwigExtension;

/**
 * Class AdminController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 *
 * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
 */
class AdminController extends BaseController
{

    private $smileyTwigExtension;

    public function __construct(SmileyTwigExtension $smileyTwigExtension)
    {
        $this->smileyTwigExtension = $smileyTwigExtension;
    }


    /** @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @return Response
     * @throws \Exception
     */
    public function indexAction()
    {
        $list_forum = $this->em->getRepository('YosimitsoWorkingForumBundle:Forum')->findAll();

        $settingsList = [
            'allow_anonymous_read' => ['varType' => 'boolean'],
            'allow_moderator_delete_thread' => ['varType' => 'boolean'],
            'theme_color' => ['varType' => 'string'],
            'lock_thread_older_than' => ['varType' => 'number'],
        ];

        $settings_render = $this->renderSettings($settingsList);
        $newPostReported = count(
            $this->em->getRepository('YosimitsoWorkingForumBundle:PostReport')
                ->findBy(['processed' => null])
        );

        return $this->render(
            '@YosimitsoWorkingForum/Admin/main.html.twig',
            [
                'list_forum' => $list_forum,
                'settings_render' => $settings_render,
                'newPostReported' => $newPostReported,
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function editAction(Request $request, $id)
    {
        $forum = $this->em->getRepository('YosimitsoWorkingForumBundle:Forum')->find($id);

        $statistics = ['nbThread' => 0, 'nbPost' => 0];
        foreach ($forum->getSubforum() as $subforum) {
            $statistics['nbThread'] += $subforum->getNbThread();
            $statistics['nbPost'] += $subforum->getNbPost();
        }

        $statistics['averagePostThread'] = ($statistics['nbThread'] > 0) ? $statistics['nbPost'] / $statistics['nbThread'] : 0;
        $form = $this->createForm(AdminForumType::class, $forum);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($forum->getSubforum() as $subforum) {
                $subforum->setForum($forum);
            }
            $this->em->persist($forum);
            $this->em->flush();

            $this->flashbag->add(
                'success',
                $this->translator->trans('message.saved', [], 'YosimitsoWorkingForumBundle')
            );

            return $this->redirect($this->generateUrl('workingforum_admin'));

        }

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Forum/form.html.twig',
            [
                'forum' => $forum,
                'form' => $form->createView(),
                'statistics' => $statistics,
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function addAction(Request $request)
    {
        $forum = new \Yosimitso\WorkingForumBundle\Entity\Forum;
        $forum->addSubForum(new \Yosimitso\WorkingForumBundle\Entity\Subforum);

        $form = $this->createForm(AdminForumType::class, $forum);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($forum->getSubforum() as $subforum) {
                $subforum->setForum($forum);
            }
            $forum->generateSlug($forum->getName());
            $this->em->persist($forum);
            $this->em->flush();

            $this->flashbag->add(
                'success',
                $this->translator->trans('message.saved', [], 'YosimitsoWorkingForumBundle')
            );

            return $this->redirect($this->generateUrl('workingforum_admin'));
        }

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Forum/form.html.twig',
            [
                'forum' => $forum,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editRulesAction(Request $request)
    {
        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown
        $rules = new Rules();
        $langs = array_merge(['en'], (array) $this->em->getRepository('YosimitsoWorkingForumBundle:Rules')->getLangs());

        $form = $this->createForm(RulesType::class, $rules, ['langs' => $langs]);
        $form->handleRequest($request);
        $parameters  = [ // PARAMETERS USED BY TEMPLATE
            'fileUpload' => ['enable' => false],
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($rules);
            $this->em->flush();
        }

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Forum/rules.html.twig',
            [
                'form' => $form->createView(),
                'listSmiley' => $listSmiley,
                'parameters' => $parameters,
                'request' => $request
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @return Response
     */
    public function ReportAction()
    {
        $postReportList = $this->em->getRepository('YosimitsoWorkingForumBundle:PostReport')
            ->findBy(['processed' => null], ['processed' => 'ASC', 'id' => 'ASC']);
        $date_format = $this->container->getParameter('yosimitso_working_forum.date_format');

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Report/report.html.twig',
            [
                'postReportList' => $postReportList,
                'date_format' => $date_format,
            ]
        );
    }

    /**  @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     */
    public function ReportHistoryAction()
    {
        $postReportList = $this->em->getRepository('YosimitsoWorkingForumBundle:PostReport')
            ->findBy(['processed' => 1], ['processed' => 'ASC', 'id' => 'DESC']);
        $date_format = $this->getParameter('yosimitso_working_forum.date_format');

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Report/report_history.html.twig',
            [
                'postReportList' => $postReportList,
                'date_format' => $date_format,
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @param Request $request
     *
     * @return Response
     */
    public function ReportActionGoodAction(Request $request)
    {
        $id = (int)htmlentities($request->request->get('id'));

        if ($id) {
            $report = $this->em->getRepository('YosimitsoWorkingForumBundle:PostReport')->findOneById($id);
            if (is_null($report)) {
                return new Response(json_encode('fail'), 500);
            }
            $report->setProcessed(1);
            $this->em->persist($report);
        }

        $this->em->flush();

        return new Response(json_encode('ok'), 200);

    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @return Response
     */
    public function userListAction()
    {
        $usersList = $this->em->getRepository('YosimitsoWorkingForumBundle:User')->findAll();

        return $this->render(
            '@YosimitsoWorkingForum/Admin/User/userslist.html.twig',
            [
                'usersList' => $usersList,

            ]
        );

    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @param Request $request
     *
     * @return Response
     */
    public function ReportActionModerateAction(Request $request)
    {
        $reason = htmlentities($request->request->get('reason'));
        $id = (int)htmlentities($request->request->get('id'));
        $postId = (int)htmlentities($request->request->get('postId'));
        $banuser = (int)htmlentities($request->request->get('banuser'));

        if (empty($reason)) {
            return new Response(json_encode('fail'), 500);
        }

        $post = $this->em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById($postId);
        if (is_null($post)) {
            return new Response(json_encode('fail'), 500);
        }
        $post->setModerateReason($reason);
        $this->em->persist($post);

        if ($id) {
            $report = $this->em->getRepository('YosimitsoWorkingForumBundle:PostReport')->findOneById($id);
            if (is_null($report)) {
                return new Response(json_encode('fail'), 500);
            }
            $report->setProcessed(1);
            $this->em->persist($report);
        }

        if ($banuser) {
            $postUser = $this->em->getRepository('YosimitsoWorkingForumBundle:User')->findOneById($post->getUser()->getId());
            if (is_null($postUser)) {
                return new Response(json_encode('fail'), 500);
            }
            $postUser->setBanned(1);
            $this->em->persist($postUser);
        }
        $this->em->flush();

        return new Response(json_encode('ok'), 200);

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @param $forum_id
     *
     * @return Response
     */
    public function deleteForumAction($forum_id)
    {
        $forum = $this->em->getRepository('YosimitsoWorkingForumBundle:Forum')->findOneById($forum_id);

        if (!is_null($forum)) {
            $this->em->remove($forum);
            $this->em->flush();
            $this->flashbag->add(
                'success',
                $this->get('translator')->trans('admin.forumDeleted', [], 'YosimitsoWorkingForumBundle')
            );
        }

        return $this->forward('@YosimitsoWorkingForum/Admin/index', []);
    }

    private function renderSettings($settingsList)
    {
        $settingsHtml = [];

        foreach ($settingsList as $index => $setting) {
            $html = [];
            $setting['value'] = $this->getParameter('yosimitso_working_forum.'.$index);

            switch ($setting['varType']) {
                case 'boolean':
                    $setting['attr'] = ['autocomplete' => 'off', 'disabled' => 'disabled'];
                    $setting['type'] = 'checkbox';
                    break;
                case 'string':
                    $setting['attr'] = ['autocomplete' => 'off', 'disabled' => 'disabled', 'style' => 'width:80px'];
                    $setting['type'] = 'text';
                    break;
                case 'number':
                    $setting['attr'] = ['autocomplete' => 'off', 'disabled' => 'disabled'];
                    $setting['type'] = 'number';
                    break;
            }


            $html['text'] = $this->translator
                ->trans('setting.'.$index, [], 'YosimitsoWorkingForumBundle');

            $html['input'] = '<input type="'.$setting['type'].'" value="'.$setting['value'].'"';
            foreach ($setting['attr'] as $indexAttr => $attr) {
                $html['input'] .= ' '.$indexAttr.'="'.$attr.'"';
            }

            $html['input'] .= '/>';

            $settingsHtml[] = $html;
        }

        return $settingsHtml;

    }

}