<?php

namespace BarthyKoeln\ImageUploadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('barthy_koeln_image_upload');

        $treeBuilder->getRootNode()
            ->children()

            ->scalarNode('image_class')
            ->isRequired()
            ->end()

            ->scalarNode('image_path_prefix')
            ->isRequired()
            ->end()

            ->scalarNode('max_file_size')
            ->defaultValue('2M')
            ->end()

            ->scalarNode('required_translation')
            ->defaultValue(null)
            ->end()

            ->end();

        return $treeBuilder;
    }
}
