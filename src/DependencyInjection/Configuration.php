<?php

namespace ISerranoDev\EncryptBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('i_serrano_dev_encrypt');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('encryption_key_path')
                    ->defaultValue('%kernel.project_dir%/encryption/encryption.key')
                ->end()
                ->scalarNode('hash_key')
                    ->isRequired()
                ->end()
                ->scalarNode('method')
                    ->isRequired()
                ->end()
                    ->scalarNode('iv')
                ->isRequired()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}