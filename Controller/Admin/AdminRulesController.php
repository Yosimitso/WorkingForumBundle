<?php

namespace Yosimitso\WorkingForumBundle\Controller\Admin;

use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Yosimitso\WorkingForumBundle\Entity\Rules;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Yosimitso\WorkingForumBundle\Form\RulesType;
use Yosimitso\WorkingForumBundle\Form\RulesEditType;
use Yosimitso\WorkingForumBundle\Twig\Extension\SmileyTwigExtension;

/**
 * Class AdminRulesController
 *
 * @package Yosimitso\WorkingForumBundle\Controller\Admin
 *
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
 */
class AdminRulesController extends BaseController
{
    /**
     * @var SmileyTwigExtension
     */
    private $smileyTwigExtension;

    public function __construct(SmileyTwigExtension $smileyTwigExtension)
    {
        $this->smileyTwigExtension = $smileyTwigExtension;
    }

    /**
     *
     * @return mixed
     */
    public function rulesAction()
    {
        $form = $this->createForm(RulesType::class, null);

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Rules/rules.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function rulesEditAction(Request $request, $lang)
    {
        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown
        $rules = $this->em->getRepository(Rules::class)->findOneBy(['lang' => $lang]);

        if (is_null($rules)) {
            throw new \Exception('Lang not found', 500);
        }

        $form = $this->createForm(RulesEditType::class, $rules);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($rules);
            $this->em->flush();
            $this->flashbag->add(
                'success',
                $this->translator->trans('message.saved', [], 'YosimitsoWorkingForumBundle')
            );

        }

        $parameters = [ // PARAMETERS USED BY TEMPLATE
            'fileUpload' => ['enable' => false],
        ];

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Rules/rules-edit.html.twig',
            [
                'form' => $form->createView(),
                'listSmiley' => $listSmiley,
                'request' => $request,
                'lang' => $lang,
                'parameters' => $parameters,
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function rulesNewAction(Request $request, $lang)
    {
        $listSmiley = $this->smileyTwigExtension->getListSmiley(); // Smileys available for markdown
        $rules = new Rules();

        $form = $this->createForm(RulesEditType::class, $rules);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rules->setLang($lang);
            $this->em->persist($rules);
            $this->em->flush();
        }

        $parameters = [ // PARAMETERS USED BY TEMPLATE
            'fileUpload' => ['enable' => false],
        ];

        return $this->render(
            '@YosimitsoWorkingForum/Admin/Rules/rules-edit.html.twig',
            [
                'form' => $form->createView(),
                'listSmiley' => $listSmiley,
                'request' => $request,
                'parameters' => $parameters,
                'lang' => $lang,
            ]
        );
    }

}
