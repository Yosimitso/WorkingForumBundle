<?php

namespace Yosimitso\WorkingForumBundle\Controller\Admin;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_MODERATOR")'))]
class AdminUsersController extends BaseController
{
    #[Route('/admin/users', name: 'workingforum_admin_user')]
    public function userListAction(): Response
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
