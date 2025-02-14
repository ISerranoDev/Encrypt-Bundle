<?php

namespace ISerranoDev\EncryptBundle\Command;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'iserranodev:encrypt-bundle:generate-key',
    description: 'Generate a key for encrypt your data',
)]
class GenerateKeyCommand extends Command
{

    public function __construct(
        private Filesystem $filesystem,
        private string $keyPath
    ) {
        parent::__construct('generate-key');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if($this->filesystem->exists($this->keyPath)){
            $io->error('The encryption key already exists!');
            return Command::FAILURE;
        }

        // Asegurar que el directorio existe
        $directory = dirname($this->keyPath);
        if(!$this->filesystem->exists($directory)){
            $this->filesystem->mkdir($directory);
        }

        $encKey = KeyFactory::generateEncryptionKey();
        KeyFactory::save($encKey, $this->keyPath);

        $io->success('Encryption Key generated successfully.');

        return Command::SUCCESS;
    }
}
