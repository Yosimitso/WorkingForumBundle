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
                ->scalarNode('site_title')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->end()
                ->booleanNode('allow_anonymous_read')
                    ->isRequired()
                    ->end()
                ->integerNode('thread_per_page')
                    ->min(1)
                    ->defaultValue(50)
                ->end()
                ->integerNode('post_per_page')
                    ->min(1)
                    ->defaultValue(20)
                ->end()
                ->scalarNode('date_format')
                    ->defaultValue('d/m/Y')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($format) {
                            $date = '1920-01-02';
                            $d = \DateTime::createFromFormat($format, $date);
                            return $d && $d->format($format) === $date;
                        })
                        ->thenInvalid('WorkingForum Bundle : the "date_format" parameters must be a valid date format, please see available constants on : https://www.php.net/manual/en/function.date.php')
                    ->end()
                ->end()
                ->scalarNode('time_format')
                    ->defaultValue('H:i:s')
                    ->cannotBeEmpty()
                ->end()
                ->booleanNode('allow_moderator_delete_thread')
                    ->defaultFalse()
                ->end()
                ->scalarNode('theme_color')
                    ->defaultValue('green')
                    ->cannotBeEmpty()
                ->end()
                ->integerNode('lock_thread_older_than')
                    ->min(0)
                    ->defaultValue(365)
                ->end()
                ->arrayNode('vote')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('threshold_useful_post')
                        ->defaultValue(5)
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
                            ->min(1)
                            ->defaultValue(10000)
                            ->end()
                        ->arrayNode('accepted_format')
                            ->defaultValue(['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/tiff', 'application/pdf'])
                            ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()
                        ->booleanNode('preview_file')
                            ->defaultTrue()
                            ->end()
                    ->end()
                ->end()
                ->integerNode('post_flood_sec')
                    ->defaultValue(30)
                    ->min(1)
                ->end()
                ->arrayNode('thread_subscription')
                    ->children()
                        ->booleanNode('enable')
                        ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
