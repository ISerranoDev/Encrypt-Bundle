<?php


namespace ISerranoDev\EncryptBundle\Attribute;
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Hashed
{
    const name = 'Hashed';

    public function __construct(public bool $caseSensitive = false)
    {

    }

}