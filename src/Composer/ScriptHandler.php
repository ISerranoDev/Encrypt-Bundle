<?php

namespace ISerranoDev\EncryptBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler
{
    public static function removeConfig(Event $event)
    {
        $filesystem = new Filesystem();
        $projectDir = getcwd();

        try {
            // 1. Eliminar el archivo de configuración
            $configFile = $projectDir . '/config/packages/i_serrano_dev_encrypt.yaml';
            if ($filesystem->exists($configFile)) {
                $filesystem->remove($configFile);
                echo "Removed configuration file: " . $configFile . PHP_EOL;
            }

            // 2. Eliminar la entrada del bundles.php
            $bundlesFile = $projectDir . '/config/bundles.php';
            if ($filesystem->exists($bundlesFile)) {
                $contents = file_get_contents($bundlesFile);
                $bundleEntry = "ISerranoDev\\EncryptBundle\\ISerranoDEvEncryptBundle::class => ['all' => true],";
                $contents = str_replace($bundleEntry, '', $contents);
                file_put_contents($bundlesFile, $contents);
                echo "Removed bundle from bundles.php" . PHP_EOL;
            }

            // 3. Eliminar variables de entorno si existen
            $envFile = $projectDir . '/.env';
            if ($filesystem->exists($envFile)) {
                $contents = file_get_contents($envFile);
                $contents = preg_replace(
                    '/###> iserranodev\/encrypt-bundle ###.*###< iserranodev\/encrypt-bundle ###\n?/s',
                    '',
                    $contents
                );
                file_put_contents($envFile, $contents);
                echo "Removed environment variables" . PHP_EOL;
            }

            // 4. Limpiar la caché
            if (file_exists($projectDir . '/bin/console')) {
                exec('php bin/console cache:clear 2>&1', $output, $returnCode);
                if ($returnCode === 0) {
                    echo "Cache cleared successfully" . PHP_EOL;
                }
            }

        } catch (\Exception $e) {
            echo "Warning: Error during cleanup: " . $e->getMessage() . PHP_EOL;
        }
    }
}