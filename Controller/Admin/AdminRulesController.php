<?php

namespace Yosimitso\WorkingForumBundle\Controller\Admin;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Yosimitso\WorkingForumBundle\Entity\Rules;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Yosimitso\WorkingForumBundle\Form\RulesType;
use Yosimitso\WorkingForumBundle\Form\RulesEditType;
use Yosimitso\WorkingForumBundle\Twig\Extension\SmileyTwigExtension;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN")'))]
class AdminRulesController extends BaseController
{
    private SmileyTwigExtension $smileyTwigExtension;

    public function __construct(SmileyTwigExtension $smileyTwigExtension)
    {
        $this->smileyTwigExtension = $smileyTwigExtension;
    }

    #[Route('/admin/rules', name: 'workingforum_admin_forum_rules')]
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

    #[Route('/admin/rules/edit/{lang}', name: 'workingforum_admin_edit_forum_rules')]
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

    #[Route('/admin/rules/new/{lang}', name: 'workingforum_admin_new_forum_rules')]
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
