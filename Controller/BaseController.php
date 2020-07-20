<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuardInterface;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * Class BaseController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class BaseController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var AuthorizationGuardInterface
     */
    protected $authorizationGuard;
    /**
     * @var UserInterface|null
     */
    protected $user;
    /**
     * @var FlashBagInterface
     */
    protected $flashbag;
    /**
     * @var DataCollectorTranslator
     */
    protected $translator;
    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    protected $templating;
    
    public function setParameters(
        EntityManagerInterface $em,
        AuthorizationGuardInterface $authorizationGuard,
        $user,
        SessionInterface $session,
        $translator,
        PaginatorInterface $paginator,
        $templating
    ) {
        $this->em = $em;
        $this->authorizationGuard = $authorizationGuard;
        $this->user = (is_object($user)) ? $user : null;
        $this->flashbag = $session->getFlashBag();
        $this->translator = $translator;
        $this->paginator = $paginator;
        $this->templating = $templating;
    }
}
