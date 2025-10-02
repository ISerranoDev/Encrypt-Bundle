<?php


namespace ISerranoDev\EncryptBundle\Attribute;
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Encrypted
{
    const name = 'Encrypted';

    public function __construct(public bool $caseSensitive = false)
    {

    }

}