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

        // Ruta al archivo de configuración
        $configFile = $projectDir . '/config/packages/i_serrano_dev_encrypt.yaml';

        try {
            if ($filesystem->exists($configFile)) {
                $filesystem->remove($configFile);
                echo "Removed configuration file: " . $configFile . PHP_EOL;
            }

            // Eliminar la entrada del bundles.php
            $bundlesFile = $projectDir . '/config/bundles.php';
            if ($filesystem->exists($bundlesFile)) {
                $contents = file_get_contents($bundlesFile);
                // Reemplaza la línea que contiene tu bundle
                $contents = preg_replace(
                    "/.*ISerranoDev\\\\EncryptBundle\\\\ISerranoDEvEncryptBundle::class => .*,\n?/m",
                    '',
                    $contents
                );
                file_put_contents($bundlesFile, $contents);
                echo "Removed bundle from bundles.php" . PHP_EOL;
            }
        } catch (\Exception $e) {
            echo "Warning: Error during cleanup: " . $e->getMessage() . PHP_EOL;
        }
    }
}