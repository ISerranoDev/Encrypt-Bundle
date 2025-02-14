<?php

namespace ISerranoDev\EncryptBundle\Doctrine\Migrations;

use ISerranoDev\EncryptBundle\Service\EncryptService;
use ISerranoDev\EncryptBundle\Interface\EncryptAwareMigrationInterface;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class EncryptAwareMigrationFactory implements MigrationFactory
{

    public function __construct(
        private readonly Connection      $connection,
        private readonly LoggerInterface $logger,
        private EncryptService           $encryptService
    )
    {
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $migration = new $migrationClassName(
            $this->connection,
            $this->logger
        );

        if ($migration instanceof EncryptAwareMigrationInterface) {
            $migration->setEncryptService($this->encryptService);
        }

        return $migration;
    }
}