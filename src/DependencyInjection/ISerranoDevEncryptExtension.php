<?php

namespace ISerranoDev\EncryptBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Filesystem\Filesystem;

class ISerranoDevEncryptExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('i_serrano_dev_encrypt.encryption_key_path', $config['encryption_key_path']);
        $container->setParameter('i_serrano_dev_encrypt.hash_key', $config['hash_key']);
        $container->setParameter('i_serrano_dev_encrypt.method', $config['method']);
        $container->setParameter('i_serrano_dev_encrypt.iv', $config['iv']);

        // Create necessary files
        $this->initializeBundle($container->getParameter('kernel.project_dir'));
    }

    private function initializeBundle(string $projectDir): void
    {
        $filesystem = new Filesystem();

        // Create .env variables if they don't exist
        $envFile = $projectDir . '/.env';
        $bundleEnvContent = <<<EOT

###> iserranodev/encrypt-bundle ###
ISD_ENCRYPT_HASH_KEY=TuHashKeyAqui
ISD_ENCRYPT_METHOD=AES-128-CBC
ISD_ENCRYPT_IV=TuIVAqui
###< iserranodev/encrypt-bundle ###
EOT;

        if ($filesystem->exists($envFile)) {
            $currentEnvContent = file_get_contents($envFile);
            if (strpos($currentEnvContent, 'iserranodev/encrypt-bundle') === false) {
                file_put_contents($envFile, $currentEnvContent . $bundleEnvContent);
            }
        } else {
            file_put_contents($envFile, $bundleEnvContent);
        }

        // Create config file if it doesn't exist
        $configDir = $projectDir . '/config/packages';
        $configFile = $configDir . '/i_serrano_dev_encrypt.yaml';

        if (!$filesystem->exists($configFile)) {
            if (!$filesystem->exists($configDir)) {
                $filesystem->mkdir($configDir);
            }

            $configContent = <<<EOT
i_serrano_dev_encrypt:
    hash_key: '%env(resolve:ISD_ENCRYPT_HASH_KEY)%'
    method: '%env(resolve:ISD_ENCRYPT_METHOD)%'
    iv: '%env(resolve:ISD_ENCRYPT_IV)%'
EOT;

            file_put_contents($configFile, $configContent);
        }
    }
}