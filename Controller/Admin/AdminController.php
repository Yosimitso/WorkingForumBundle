<?php

namespace Yosimitso\WorkingForumBundle\Controller\Admin;

use Yosimitso\WorkingForumBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\PostReport;

/**
 * Class AdminController
 *
 * @package Yosimitso\WorkingForumBundle\Controller\Admin
 *
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
 */
class AdminController extends BaseController
{

    /** @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MODERATOR')")
     * @return Response
     * @throws \Exception
     */
    public function indexAction()
    {
        $list_forum = $this->em->getRepository(Forum::class)->findAll();

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
            $this->em->getRepository(PostReport::class)
                ->findBy(['processed' => null])
        );

        return $this->render(
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
                    $setting['value'] = $this->bundleParameters->$setting['label'][$setting['key']];
                    $html['text'] = $this->translator
                        ->trans('setting.'.$setting['label'].'.'.$setting['key'], [], 'YosimitsoWorkingForumBundle');
                } else {
                    $setting['value'] = $this->bundleParameters->$setting['label'];

                    $html['text'] = $this->translator
                        ->trans('setting.'.$setting['label'], [], 'YosimitsoWorkingForumBundle');
                }

                switch ($setting['varType']) {
                    case 'boolean':
                        $setting['attr'] = ['autocomplete' => 'off', 'disabled' => 'disabled'];
                        $setting['type'] = 'checkbox';
                        if ($setting['value']) {
                            $setting['attr']['checked'] = 'checked';
                        }
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