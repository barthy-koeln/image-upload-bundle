<?php

namespace Barthy\ImageUploadBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BarthyImageUploadExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Process internal config files
        $yamlLoader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__."/../Resources/config")
        );

        $yamlLoader->load("services.yaml");

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->getDefinition(ImageUploadConfig::class)->setArgument(0, $config);
    }
}