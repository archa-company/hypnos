<?php

namespace Morpheus\Contracts;

interface Actionable
{
    public function __invoke(...$params): void;
}
