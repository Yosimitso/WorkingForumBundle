<?php

namespace Yosimitso\WorkingForumBundle\Controller\Admin;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_MODERATOR")'))]
class AdminReportController extends BaseController
{
    #[Route('/admin/report', name: 'workingforum_admin_report')]
    public function reportAction(): Response
    {
        $postReportList = $this->em->getRepository(PostReport::class)
            ->findBy(['processed' => null], ['processed' => 'ASC', 'id' => 'ASC']);
        $date_format = $this->bundleParameters->date_format;

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Report/report.html.twig',
            [
                'postReportList' => $postReportList,
                'date_format' => $date_format,
            ]
        );
    }

    #[Route('/admin/report/history', name: 'workingforum_admin_report_history')]
    public function reportHistoryAction(): Response
    {
        $postReportList = $this->em->getRepository(PostReport::class)
            ->findBy(['processed' => 1], ['processed' => 'ASC', 'id' => 'DESC']);
        $date_format = $this->bundleParameters->date_format;

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Report/report_history.html.twig',
            [
                'postReportList' => $postReportList,
                'date_format' => $date_format,
            ]
        );
    }

    #[Route('/admin/reportaction/good', name: 'workingforum_admin_report_action_good')]
    public function reportActionGoodAction(Request $request): Response
    {
        $id = (int)htmlentities($request->request->get('id'));

        if ($id) {
            $report = $this->em->getRepository(PostReport::class)->findOneById($id);
            if (is_null($report)) {
                return new Response(json_encode('fail'), 500);
            }
            $report->setProcessed(1);
            $this->em->persist($report);
        }

        $this->em->flush();

        return new Response(json_encode('ok'), 200);
    }

    #[Route('/admin/reportaction/moderate', name: 'workingforum_admin_report_action_moderate')]
    public function reportActionModerateAction(Request $request): Response
    {
        $reason = htmlentities($request->request->get('reason'));
        $id = (int)htmlentities($request->request->get('id'));
        $postId = (int)htmlentities($request->request->get('postId'));
        $banuser = (int)htmlentities($request->request->get('banuser'));

        if (empty($reason)) {
            return new Response(json_encode('fail'), 500);
        }

        $post = $this->em->getRepository(Post::class)->findOneById($postId);
        if (is_null($post)) {
            return new Response(json_encode('fail'), 500);
        }
        $post->setModerateReason($reason);
        $this->em->persist($post);

        if ($id) {
            $report = $this->em->getRepository(PostReport::class)->findOneById($id);
            if (is_null($report)) {
                return new Response(json_encode('fail'), 500);
            }
            $report->setProcessed(1);
            $this->em->persist($report);
        }

        if ($banuser) {
            $postUser = $this->em->getRepository(UserInterface::class)->findOneById(
                $post->getUser()->getId()
            );
            if (is_null($postUser)) {
                return new Response(json_encode('fail'), 500);
            }
            $postUser->setBanned(1);
            $this->em->persist($postUser);
        }
        $this->em->flush();

        return new Response(json_encode('ok'), 200);
    }
}
