<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 *
 * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
 */
class AdminController extends Controller
{
    /** @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $list_forum = $em->getRepository('YosimitsoWorkingForumBundle:Forum')->findAll();

        $settingsList = [
            'allow_anonymous_read' => ['varType' => 'boolean'],
            'allow_moderator_delete_thread' => ['varType' => 'boolean'],
            'theme_color' => ['varType' => 'string'],
            'lock_thread_older_than' => ['varType' => 'number'],
        ];

        $settings_render = $this->renderSettings($settingsList);
        $newPostReported = count(
            $em->getRepository('YosimitsoWorkingForumBundle:PostReport')
                ->findBy(['processed' => null])
        );

        return $this->render(
            'YosimitsoWorkingForumBundle:Admin:main.html.twig',
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
        $em = $this->getDoctrine()->getManager();
        $forum = $em->getRepository('YosimitsoWorkingForumBundle:Forum')->find($id);

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
            $em->persist($forum);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('message.saved', [], 'YosimitsoWorkingForumBundle')
            );

            return $this->redirect($this->generateUrl('workingforum_admin'));

        }

        return $this->render(
            'YosimitsoWorkingForumBundle:Admin/Forum:form.html.twig',
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
        $em = $this->getDoctrine()->getManager();
        $forum = new \Yosimitso\WorkingForumBundle\Entity\Forum;
        $forum->addSubForum(new \Yosimitso\WorkingForumBundle\Entity\Subforum);

        $form = $this->createForm(AdminForumType::class, $forum);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($forum->getSubforum() as $subforum) {
                $subforum->setForum($forum);
            }
            $forum->generateSlug($forum->getName());
            $em->persist($forum);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('message.saved', [], 'YosimitsoWorkingForumBundle')
            );

            return $this->redirect($this->generateUrl('workingforum_admin'));
        }

        return $this->render(
            'YosimitsoWorkingForumBundle:Admin/Forum:form.html.twig',
            [
                'forum' => $forum,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @return Response
     */
    public function ReportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $postReportList = $em->getRepository('YosimitsoWorkingForumBundle:PostReport')
            ->findBy(['processed' => null], ['processed' => 'ASC', 'id' => 'ASC']);
        $date_format = $this->container->getParameter('yosimitso_working_forum.date_format');

        return $this->render(
            'YosimitsoWorkingForumBundle:Admin/Report:report.html.twig',
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
        $em = $this->getDoctrine()->getManager();
        $postReportList = $em->getRepository('YosimitsoWorkingForumBundle:PostReport')
            ->findBy(['processed' => 1], ['processed' => 'ASC', 'id' => 'DESC']);
        $date_format = $this->container->getParameter('yosimitso_working_forum.date_format');

        return $this->render(
            'YosimitsoWorkingForumBundle:Admin/Report:report_history.html.twig',
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
        $em = $this->getDoctrine()->getManager();

        $id = (int)htmlentities($request->request->get('id'));

        if ($id) {
            $report = $em->getRepository('YosimitsoWorkingForumBundle:PostReport')->findOneById($id);
            if (is_null($report)) {
                return new Response(json_encode('fail'), 500);
            }
            $report->setProcessed(1);
            $em->persist($report);
        }
        $em->flush();

        return new Response(json_encode('ok'), 200);

    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @return Response
     */
    public function userListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usersList = $em->getRepository('YosimitsoWorkingForumBundle:User')->findAll();

        return $this->render(
            'YosimitsoWorkingForumBundle:Admin/User:userslist.html.twig',
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
        $em = $this->getDoctrine()->getManager();
        $reason = htmlentities($request->request->get('reason'));
        $id = (int)htmlentities($request->request->get('id'));
        $postId = (int)htmlentities($request->request->get('postId'));
        $banuser = (int)htmlentities($request->request->get('banuser'));

        if (empty($reason)) {
            return new Response(json_encode('fail'), 500);
        }

        $post = $em->getRepository('YosimitsoWorkingForumBundle:Post')->findOneById($postId);
        if (is_null($post)) {
            return new Response(json_encode('fail'), 500);
        }
        $post->setModerateReason($reason);
        $em->persist($post);

        if ($id) {
            $report = $em->getRepository('YosimitsoWorkingForumBundle:PostReport')->findOneById($id);
            if (is_null($report)) {
                return new Response(json_encode('fail'), 500);
            }
            $report->setProcessed(1);
            $em->persist($report);
        }

        if ($banuser) {
            $postUser = $em->getRepository('YosimitsoWorkingForumBundle:User')->findOneById($post->getUser()->getId());
            if (is_null($postUser)) {
                return new Response(json_encode('fail'), 500);
            }
            $postUser->setBanned(1);
            $em->persist($postUser);
        }
        $em->flush();

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
        $em = $this->getDoctrine()->getManager();
        $forum = $em->getRepository('YosimitsoWorkingForumBundle:Forum')->findOneById($forum_id);

        if (!is_null($forum)) {
            $em->remove($forum);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('admin.forumDeleted', [], 'YosimitsoWorkingForumBundle')
            );
        }

        return $this->forward('YosimitsoWorkingForumBundle:Admin:index', []);
    }

    private function renderSettings($settingsList)
    {
        $settingsHtml = [];

        foreach ($settingsList as $index => $setting) {
            $html = [];
            $setting['value'] = $this->container->getParameter('yosimitso_working_forum.'.$index);

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


            $html['text'] = $this->get('translator')
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