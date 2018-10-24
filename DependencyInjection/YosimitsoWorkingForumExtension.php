<?php

namespace Yosimitso\WorkingForumBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class YosimitsoWorkingForumExtension
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @package Yosimitso\WorkingForumBundle\DependencyInjection
 */
class YosimitsoWorkingForumExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!isset($config['thread_per_page'])) {
            throw new \InvalidArgumentException(
                'The "thread_per_page" option must be set in "yosimitso_working_forum"'
            );
        }

        if (!isset($config['date_format'])) {
            throw new \InvalidArgumentException(
                'The "post_per_page" option must be set in "yosimitso_working_forum", please see README.MD'
            );
        }

        if (!isset($config['allow_anonymous_read'])) {
            throw new \InvalidArgumentException(
                'The "allow_anonymous_read" option must be set in "yosimitso_working_forum", please see README.MD'
            );
        }

        if (!isset($config['theme_color'])) {
            throw new \InvalidArgumentException(
                'The "theme_color" option must be set in "yosimitso_working_forum", please see README.MD'
            );
        }

        if (!isset($config['lock_thread_older_than'])) {
            throw new \InvalidArgumentException(
                'The "lock_thread_older_than" option must be set in "yosimitso_working_forum", please see README.MD'
            );
        }

        if (!isset($config['vote'])) {
            throw new \InvalidArgumentException(
                'The "vote" array option must be set in "yosimitso_working_forum", please see README.MD'
            );
        }

        if (!isset($config['file_upload'])) {
            throw new \InvalidArgumentException(
                'The "file_upload" array option must be set in "yosimitso_working_forum", please see README.MD'
            );
        }

        if (!isset($config['post_flood_sec'])) {
            throw new \InvalidArgumentException(
                'The "post_flood_sec" option must be set in "yosimitso_working_forum", please see README.MD'
            ); 
        }

        if (!isset($config['site_title'])) {
            throw new \InvalidArgumentException(
                'The "site_title" option must be set in "yosimitso_working_forum", please see README.MD'
            );
        }

        if (!isset($config['thread_subscription'])) {
            throw new \InvalidArgumentException(
                'The "thread_subscription" option must be set in "yosimitso_working_forum", please see README.MD'
            );
        }

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        $container->setParameter('yosimitso_working_forum.thread_per_page', $config['thread_per_page']);
        $container->setParameter('yosimitso_working_forum.post_per_page', $config['post_per_page']);
        $container->setParameter('yosimitso_working_forum.date_format', $config['date_format']);
        $container->setParameter('yosimitso_working_forum.allow_anonymous_read', $config['allow_anonymous_read']);
        $container->setParameter('yosimitso_working_forum.allow_moderator_delete_thread', $config['allow_moderator_delete_thread']);
        $container->setParameter('yosimitso_working_forum.theme_color', $config['theme_color']);
        $container->setParameter('yosimitso_working_forum.lock_thread_older_than', $config['lock_thread_older_than']);
        $container->setParameter('yosimitso_working_forum.vote', $config['vote']);
        $container->setParameter('yosimitso_working_forum.file_upload', $config['file_upload']);
        $container->setParameter('yosimitso_working_forum.post_flood_sec', $config['post_flood_sec']);
        $container->setParameter('yosimitso_working_forum.site_title', $config['site_title']);
        $container->setParameter('yosimitso_working_forum.thread_subscription', $config['thread_subscription']);
    }
}
