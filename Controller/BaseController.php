<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuardInterface;
use Yosimitso\WorkingForumBundle\Service\BundleParametersService;

class BaseController extends AbstractController
{
    protected EntityManagerInterface $em;
    protected AuthorizationGuardInterface $authorizationGuard;
    protected ?UserInterface $user;
    protected FlashBagInterface $flashbag;
    protected TranslatorInterface $translator;
    protected PaginatorInterface $paginator;
    protected BundleParametersService $bundleParameters;
    protected Environment $twig;
    protected FormFactory $formFactory;

    public function setParameters(
        EntityManagerInterface $em,
        AuthorizationGuardInterface $authorizationGuard,
        ?TokenInterface $token,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        PaginatorInterface $paginator,
        BundleParametersService $bundleParameters,
        Environment $twig,
        FormFactory $formFactory
    ) {
        $this->em = $em;
        $this->authorizationGuard = $authorizationGuard;
        $this->user = (is_object($token) && $token->getUser() instanceof UserInterface) ? $token->getUser() : null;
        $this->flashbag = $requestStack->getSession()->getFlashBag();
        $this->translator = $translator;
        $this->paginator = $paginator;
        $this->bundleParameters = $bundleParameters;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    protected function isUserAnonymous(): bool
    {
        return !$this->user instanceof UserInterface;
    }

    protected function render($name, array $context = [], Response $response = null): Response
    {
        return new Response($this->twig->render($name, $context));

    }

    protected function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }
}
