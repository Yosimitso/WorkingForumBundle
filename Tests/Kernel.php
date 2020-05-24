<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosimitso\WorkingForumBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Hautelook\AliceBundle\HautelookAliceBundle;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Yosimitso\WorkingForumBundle\YosimitsoWorkingForumBundle;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        $contents = [
            FrameworkBundle::class => ['all' => true],
            SensioFrameworkExtraBundle::class => ['all' => true],
            YosimitsoWorkingForumBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            KnpPaginatorBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            SwiftmailerBundle::class => ['all' => true],
            HautelookAliceBundle::class => ['all' => true],
            FidryAliceDataFixturesBundle::class => ['all' => true],
            NelmioAliceBundle::class => ['all' => true],
        ];

        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }

    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
//        $a = $routes->import(__DIR__.'/routes.yaml', '/');


        $routes->import(__DIR__.'/../Controller', '/', 'annotation');
    }

    public function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yaml');
        $loader->load(__DIR__.'/../Resources/config/services.yml');
        // TODO: Implement configureContainer() method.
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {

        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => 'kernel::loadRoutes',
                    'type' => 'service',
                ],
            ]);

            if (!$container->hasDefinition('kernel')) {
                $container->register('kernel', static::class)
                    ->setSynthetic(true)
                    ->setPublic(true)
                ;
            }

            $kernelDefinition = $container->getDefinition('kernel');
            $kernelDefinition->addTag('routing.route_loader');

            if ($this instanceof EventSubscriberInterface) {
                $kernelDefinition->addTag('kernel.event_subscriber');
            }

            $this->configureContainer($container, $loader);

            $container->addObjectResource($this);
        });
        $this->loadRoutes($loader);
    }

    public function getProjectDir()
    {
        return __DIR__.'/../';
    }

    public function loadRoutes(LoaderInterface $loader)
    {
        $routes = new RouteCollectionBuilder($loader);
        $this->configureRoutes($routes);

        return $routes->build();
    }
}