<?php

namespace Yosimitso\WorkingForumBundle\Controller\Api;

use Doctrine\ORM\Mapping\Driver\AttributeReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Rules;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Form\RulesType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Yosimitso\WorkingForumBundle\Service\BundleParametersService;
use Yosimitso\WorkingForumBundle\Util\ApiHelper;
use Yosimitso\WorkingForumBundle\Controller\BaseController;

class ParameterController extends BaseController
{
    public function __construct(
        BundleParametersService $bundleParametersService
    )
    {
        
    }
    #[Route('/api/parameters', methods: ['GET'], name: 'workingforum_parameters')]
    public function getBundleParametersAction(): Response
    {
        $parameters = [
            'thread_per_page' => $this->bundleParameters->thread_per_page,
            'post_per_page' => $this->bundleParameters->post_per_page,
            'date_format' => $this->bundleParameters->date_format,
            'time_format' => $this->bundleParameters->time_format,
            'allow_anonymous_read' => $this->bundleParameters->allow_anonymous_read,
            'allow_moderator_delete_thread' => $this->bundleParameters->allow_moderator_delete_thread,
            'theme_color' => $this->bundleParameters->theme_color,
            'lock_thread_older_than' => $this->bundleParameters->lock_thread_older_than,
            'site_title' => $this->bundleParameters->site_title,
            'vote' => [
                'threshold_useful_post' => $this->bundleParameters->vote['threshold_useful_post']
            ],
            'file_upload' => [
                'enable' => $this->bundleParameters->file_upload['enable'],
                'max_size_ko' => $this->bundleParameters->file_upload['max_size_ko'],
                'accepted_format' => $this->bundleParameters->file_upload['accepted_format'],
                'preview_file' => $this->bundleParameters->file_upload['preview_file']
            ],
            'post_flood_sec' => $this->bundleParameters->post_flood_sec,
            'thread_subscription' => [
                'enable' => $this->bundleParameters->thread_subscription['enable']
            ],
            'mailer_sender_address' => $this->bundleParameters->mailer_sender_address
        ];
        return new JsonResponse(ApiHelper::getSerializer()->serialize($parameters, 'json'), 200, [], true);
    }
}

/*
 *     thread_per_page: 10
    post_per_page: 5
    date_format: 'd/m/Y'
    time_format: ' H:i:s'
    allow_anonymous_read: true
    allow_moderator_delete_thread: true
    theme_color: green
    lock_thread_older_than: 600
    site_title: ok
    vote:
        threshold_useful_post: 5
    file_upload:
        enable: true
        max_size_ko: 10000
        accepted_format: [image/jpg, image/jpeg, image/png, image/gif, image/tiff, application/pdf]
        preview_file: true
    post_flood_sec: 30
    thread_subscription:
        enable: true
    mailer_sender_address: test@charlymartins.fr
 */
