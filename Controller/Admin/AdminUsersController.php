<?php

namespace Yosimitso\WorkingForumBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;

/**
 * Class AdminUsersController
 *
 * @package Yosimitso\WorkingForumBundle\Controller\Admin
 *
 * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
 */
class AdminUsersController extends BaseController
{
    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @return Response
     */
    public function userListAction()
    {
        $usersList = $this->em->getRepository(UserInterface::class)->findAll();

        return $this->templating->renderResponse(
            '@YosimitsoWorkingForum/Admin/Users/userslist.html.twig',
            [
                'usersList' => $usersList,

            ]
        );

    }
}
