<?php

namespace Yosimitso\WorkingForumBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link
 * http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @package Yosimitso\WorkingForumBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('yosimitso_working_forum');
        $rootNode
            ->children()
                ->integerNode('thread_per_page')
                    ->isRequired()
                    ->min(1)
                ->end()
                ->integerNode('post_per_page')
                    ->isRequired()
                    ->min(1)
                ->end()
                ->scalarNode('date_format')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->booleanNode('allow_anonymous_read')
                    ->isRequired()
                ->end()
                ->booleanNode('allow_moderator_delete_thread')
                    ->isRequired()
                ->end()
                ->enumNode('theme_color')
                    ->values(['green', 'dark_blue'])
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->integerNode('lock_thread_older_than')
                    ->isRequired()
                    ->min(0)
                ->end()
                ->arrayNode('vote')
                    ->children()
                        ->integerNode('threshold_useful_post')
                        ->isRequired()
                        ->min(1)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('file_upload')
                    ->children()
                        ->booleanNode('enable')
                            ->isRequired()
                            ->end()
                        ->integerNode('max_size_ko')
                            ->isRequired()
                            ->min(1)
                            ->end()
                        ->arrayNode('accepted_format')
                            ->isRequired()
                            ->prototype('scalar')->end()
                            ->end()
                        ->booleanNode('preview_file')
                            ->isRequired()
                            ->end()
                    ->end()
                ->end()
                ->integerNode('post_flood_sec')
                    ->isRequired()
                    ->min(1)
                ->end()
                ->scalarNode('site_title')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('thread_subscription')
                    ->children()
                        ->booleanNode('enable')
                        ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->children()
                        ->booleanNode('enable')
                            ->isRequired()
                        ->end()
                        ->scalarNode('type')
                            ->defaultValue(null)
                        ->end()
                        ->arrayNode('params')
                            ->children()
                                ->scalarNode('host')
                                    ->defaultValue('localhost')
                                ->end()
                                ->integerNode('port')
                                    ->defaultValue(6379)
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('key_prefix')
                            ->defaultValue('wf_')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
