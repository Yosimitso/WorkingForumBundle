<?php

namespace Yosimitso\WorkingForumBundle\Controller\Admin;

use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminForumController
 *
 * @package Yosimitso\WorkingForumBundle\Controller\Admin
 *
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
 */
class AdminForumController extends BaseController
{
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function editAction(Request $request, $id)
    {
        $forum = $this->em->getRepository(Forum::class)->find($id);

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
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function addAction(Request $request)
    {
        $forum = new Forum;
        $forum->addSubForum(new Subforum);

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
     * @Security("is_granted('ROLE_ADMIN')")
     * @param int $forum_id
     *
     * @return Response
     */
    public function deleteForumAction($forum_id)
    {
        $forum = $this->em->getRepository(Forum::class)->findOneById($forum_id);

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
}
