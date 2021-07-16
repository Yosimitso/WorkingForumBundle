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
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
 */
class AdminUsersController extends BaseController
{
    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
     * @return Response
     */
    public function userListAction()
    {
        $usersList = $this->em->getRepository(UserInterface::class)->findAll();

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Users/userslist.html.twig',
            [
                'usersList' => $usersList,

            ]
        );

    }
}
