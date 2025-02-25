<?php

namespace ISerranoDev\EncryptBundle\EventListener;

use ISerranoDev\EncryptBundle\Attribute\Encrypted;
use ISerranoDev\EncryptBundle\Attribute\Hashed;
use ISerranoDev\EncryptBundle\Service\EncryptService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use ReflectionClass;

#[AsEntityListener(event: Events::preFlush, method: 'preFlush')]
#[AsEntityListener(event: Events::postLoad, method: 'postLoad')]
class HashListener
{
    public function __construct(
        private readonly EncryptService $encryptService,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function preFlush($entity, PreFlushEventArgs $args): void
    {
        $this->processEntity($entity);
    }

    public function postLoad($entity, PostLoadEventArgs $args): void
    {
        $this->processEntityDecrypt($entity);
    }

    private function processEntity($entity): void
    {
        $reflect = new ReflectionClass($entity);

        if($entity instanceof Proxy){
            $this->entityManager->getUnitOfWork()->initializeObject($entity);
            $reflect = new ReflectionClass(get_parent_class($entity));
        }

        foreach ($reflect->getProperties() as $property) {
            if($property->getAttributes(Hashed::class)){
                $propertyName = $property->getName();
                $setMethod = "set" . ucfirst($propertyName);
                $getMethod = "get" . ucfirst($propertyName);
                if(
                    method_exists($entity, $setMethod) &&
                    method_exists($entity, $getMethod) &&
                    $entity->$getMethod() != null
                ){
                    if(!$this->encryptService->isHashed($entity->$getMethod())){
                        $entity->$setMethod($this->encryptService->hashData(strtoupper($entity->$getMethod())));
                    }
                }
            }
        }
    }

    private function processEntityDecrypt($entity): void
    {
        $reflect = new ReflectionClass($entity);

        if($entity instanceof Proxy){
            $this->entityManager->getUnitOfWork()->initializeObject($entity);
            $reflect = new ReflectionClass(get_parent_class($entity));
        }

        foreach ($reflect->getProperties() as $property) {
            if($property->getAttributes(Hashed::class)){
                $propertyName = $property->getName();
                $setMethod = "set" . ucfirst($propertyName);
                $getMethod = "get" . ucfirst($propertyName);
                if(
                    method_exists($entity, $setMethod) &&
                    method_exists($entity, $getMethod) &&
                    $entity->$getMethod() != null
                ){
                    $entity->$setMethod($this->encryptService->unHashData($entity->$getMethod()));
                }
            }
        }
    }
}