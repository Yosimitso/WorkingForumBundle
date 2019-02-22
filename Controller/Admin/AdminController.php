<?php

namespace Yosimitso\WorkingForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yosimitso\WorkingForumBundle\Entity\Rules;
use Yosimitso\WorkingForumBundle\Form\AdminForumType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Form\RulesType;
use Yosimitso\WorkingForumBundle\Form\RulesEditType;
use Yosimitso\WorkingForumBundle\Twig\Extension\SmileyTwigExtension;

/**
 * Class AdminController
 *
 * @package Yosimitso\WorkingForumBundle\Controller
 *
 * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
 */
class AdminController extends BaseController
{

    private $smileyTwigExtension;

    public function __construct(SmileyTwigExtension $smileyTwigExtension)
    {
        $this->smileyTwigExtension = $smileyTwigExtension;
    }


    /** @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MODERATOR')")
     * @return Response
     * @throws \Exception
     */
    public function indexAction()
    {
        $list_forum = $this->em->getRepository('YosimitsoWorkingForumBundle:Forum')->findAll();

        $settingsList = [
            ['label' => 'allow_anonymous_read', 'varType' => 'boolean'],
            ['label' => 'allow_moderator_delete_thread', 'varType' => 'boolean'],
            ['label' => 'theme_color', 'varType' => 'string'],
            ['label' => 'lock_thread_older_than', 'varType' => 'number'],
            ['label' => 'post_flood_sec', 'varType' => 'number'],
            ['label' => 'vote', 'key' => 'threshold_useful_post', 'varType' => 'number'],
            ['label' => 'file_upload.title', 'group' => true],
            ['label' => 'file_upload', 'key' => 'enable', 'varType' => 'boolean'],
            ['label' => 'file_upload', 'key' => 'max_size_ko', 'varType' => 'number'],
            ['label' => 'file_upload', 'key' => 'accepted_format', 'varType' => 'array'],
            ['label' => 'file_upload', 'key' => 'preview_file', 'varType' => 'boolean'],


        ];

        $settings_render = $this->renderSettings($settingsList);
        $newPostReported = count(
            $this->em->getRepository('YosimitsoWorkingForumBundle:PostReport')
                ->findBy(['processed' => null])
        );

        return $this->templating->renderResponse(
            '@YosimitsoWorkingForum/Admin/main.html.twig',
            [
                'list_forum' => $list_forum,
                'settings_render' => $settings_render,
                'newPostReported' => $newPostReported,
            ]
        );
    }

    private function renderSettings($settingsList)
    {
        $settingsHtml = [];

        foreach ($settingsList as $setting) {
            if (isset($setting['group']) && $setting['group']) {
                $settingsHtml[] = [
                    'group' => true,
                    'label' => $this->translator->trans('setting.'.$setting['label'], [], 'YosimitsoWorkingForumBundle'),
                ];
            } else {
                $html = [];

                if (isset($setting['key'])) {
                    $setting['value'] = $this->getParameter(
                        'yosimitso_working_forum.'.$setting['label']
                    )[$setting['key']];
                    $html['text'] = $this->translator
                        ->trans('setting.'.$setting['label'].'.'.$setting['key'], [], 'YosimitsoWorkingForumBundle');
                } else {
                    $setting['value'] = $this->getParameter('yosimitso_working_forum.'.$setting['label']);
                    $html['text'] = $this->translator
                        ->trans('setting.'.$setting['label'], [], 'YosimitsoWorkingForumBundle');
                }

                switch ($setting['varType']) {
                    case 'boolean':
                        $setting['attr'] = ['autocomplete' => 'off', 'disabled' => 'disabled'];
                        $setting['type'] = 'checkbox';
                        break;
                    case 'string':
                        $setting['attr'] = ['autocomplete' => 'off', 'disabled' => 'disabled', 'style' => 'width:80px'];
                        $setting['type'] = 'text';
                        break;
                    case 'number':
                        $setting['attr'] = ['autocomplete' => 'off', 'disabled' => 'disabled'];
                        $setting['type'] = 'number';
                        break;
                    case 'array':
                        $setting['attr'] = ['autocomplete' => 'off', 'disabled' => 'disabled', 'style' => 'width:auto'];
                        $setting['type'] = 'text';
                        $setting['value'] = implode(',', $setting['value']);
                }


                $html['input'] = '<input type="'.$setting['type'].'" value="'.$setting['value'].'"';
                foreach ($setting['attr'] as $indexAttr => $attr) {
                    $html['input'] .= ' '.$indexAttr.'="'.$attr.'"';
                }

                $html['input'] .= '/>';

                $settingsHtml[] = $html;
            }

        }

        return $settingsHtml;

    }

}