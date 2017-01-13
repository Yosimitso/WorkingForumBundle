<?php

namespace Yosimitso\WorkingForumBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
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
        $rootNode->children()->scalarNode('thread_per_page')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                        ->end()
                             ->scalarNode('post_per_page')
                                        ->isRequired()
                                        ->cannotBeEmpty()
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
                                        ->end();
                            ->scalarNode('theme_color')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                        ->end()
        
        
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
