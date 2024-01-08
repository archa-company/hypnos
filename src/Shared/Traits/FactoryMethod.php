<?php

namespace Morpheus\Shared\Traits;

trait FactoryMethod
{
    public static function factory()
    {
        return new self;
    }
}
