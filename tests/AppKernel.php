<?php

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class AppKernel extends Kernel
{

    use MicroKernelTrait;

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Prezent\Doctrine\TranslatableBundle\PrezentDoctrineTranslatableBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new A2lix\AutoFormBundle\A2lixAutoFormBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Barthy\SlugFilenameBundle\BarthySlugFilenameBundle(),
            new Barthy\ImageUploadBundle\BarthyImageUploadBundle(),
        );

        return $bundles;
    }

    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // We don't need that Environment stuff, just one config
        $loader->load(__DIR__.'/config.yaml');
    }

    /**
     * Add or import routes into your application.
     *
     *     $routes->import('config/routing.yml');
     *     $routes->add('/admin', 'App\Controller\AdminController::dashboard', 'admin_dashboard');
     *
     * @param \Symfony\Component\Routing\RouteCollectionBuilder $routes
     *
     * @throws \Symfony\Component\Config\Exception\LoaderLoadException
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->import('@LiipImagineBundle/Resources/config/routing.xml');
    }

    /**
     * Configures the container.
     *
     * You can register extensions:
     *
     *     $c->loadFromExtension('framework', [
     *         'secret' => '%secret%'
     *     ]);
     *
     * Or services:
     *
     *     $c->register('halloween', 'FooBundle\HalloweenProvider');
     *
     * Or parameters:
     *
     *     $c->setParameter('halloween', 'lot of fun');
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $c
     * @param LoaderInterface                                         $loader
     */
    protected function configureContainer(
        ContainerBuilder $c,
        LoaderInterface $loader
    ) {
    }
}
