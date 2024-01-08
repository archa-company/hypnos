<?php

namespace Morpheus\Contracts;

interface Importable
{
    public function import(array $data): void;
}
