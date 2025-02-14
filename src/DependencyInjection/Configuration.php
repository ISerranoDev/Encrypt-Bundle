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
                    ->defaultValue('YOUR_HASH_KEY')
                ->end()
                ->scalarNode('method')
                    ->defaultValue('YOUR_HASH_METHOD')
                ->end()
                ->scalarNode('iv')
                    ->defaultValue('YOUR_HASH_IV')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}