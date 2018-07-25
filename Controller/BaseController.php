<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Security\Authorization;

/**
 * Class BaseController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 */
class BaseController extends Controller
{
    protected $em;
    protected $authorization;
    protected $user;
    protected $flashbag;
    protected $translator;
    protected $paginator;
    protected $templating;
    
    public function setParameters($em, $authorization, $user, $session, $translator, $paginator, $templating) {
        $this->em = $em;
        $this->authorization = $authorization;
        $this->user = (is_object($user)) ? $user : null;
        $this->flashbag = $session->getFlashBag();
        $this->translator = $translator;
        $this->paginator = $paginator;
        $this->templating = $templating;
    }
}