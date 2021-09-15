<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuardInterface;
use Symfony\Component\Translation\DataCollectorTranslator;
use Yosimitso\WorkingForumBundle\Service\BundleParametersService;

/**
 * Class BaseController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class BaseController extends AbstractController
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

    /**
     * @var BundleParametersService
     */
    protected $bundleParameters;
    
    public function setParameters(
        EntityManagerInterface $em,
        AuthorizationGuardInterface $authorizationGuard,
        $token,
        SessionInterface $session,
        $translator,
        PaginatorInterface $paginator,
        BundleParametersService $bundleParameters
    ) {
        $this->em = $em;
        $this->authorizationGuard = $authorizationGuard;
        $this->user = (is_object($token)) ? $token->getUser() : null;
        $this->flashbag = $session->getFlashBag();
        $this->translator = $translator;
        $this->paginator = $paginator;
        $this->bundleParameters = $bundleParameters;
    }
}
