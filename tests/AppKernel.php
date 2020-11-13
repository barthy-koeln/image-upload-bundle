<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Prezent\Doctrine\TranslatableBundle\PrezentDoctrineTranslatableBundle(),
            new \Vich\UploaderBundle\VichUploaderBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \A2lix\AutoFormBundle\A2lixAutoFormBundle(),
            new \A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \DAMA\DoctrineTestBundle\DAMADoctrineTestBundle(),
            new \BarthyKoeln\ImageUploadBundle\BarthyKoelnImageUploadBundle(),
        ];
    }

    /**
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // We don't need that Environment stuff, just one config
        $loader->load(__DIR__.'/config.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes)
    {
        $routes->import('@LiipImagineBundle/Resources/config/routing.xml');
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
    }
}
