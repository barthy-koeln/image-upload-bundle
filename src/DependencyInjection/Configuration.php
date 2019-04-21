<?php
/**
 * Created by PhpStorm.
 * User: Barthy
 * Date: 11.07.18
 * Time: 17:18
 */

namespace Barthy\ImageUploadBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('barthy_image_upload');

        $rootNode
            ->children()
                ->scalarNode('file_name_language')
                    ->defaultValue('en')
                ->end()
                ->scalarNode('max_file_size')
                    ->defaultValue('2M')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
