<?php

namespace Morpheus\Contracts;

interface Filterable
{
    public function __invoke(...$params);
}
