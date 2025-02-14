<?php

namespace ISerranoDev\EncryptBundle\Interface;

use ISerranoDev\EncryptBundle\Service\EncryptService;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface EncryptAwareMigrationInterface
{
    public function setEncryptService(EncryptService $encryptService);
}