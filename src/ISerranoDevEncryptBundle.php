<?php

namespace ISerranoDev\EncryptBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ISerranoDevEncryptBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}