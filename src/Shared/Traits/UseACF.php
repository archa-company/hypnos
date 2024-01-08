<?php

namespace Morpheus\Shared\Traits;

trait UseACF
{
    public array $acfItems = [];

    public function addAcfItem($item)
    {
        if (!$this->existsAcf()) return false;
        $this->acfItems[] = $item;
    }

    public function registerAcf()
    {
        if (!$this->hasAcf()) return false;
        $basePath = MORPHEUS_CORE_PATH . "src/acf";
        foreach ($this->acfItems as $item) {
            $itemPath = (file_exists($item)) ? $item : "{$basePath}/{$item}.json";
            if (!file_exists($itemPath)) continue;
            $json = file_get_contents($itemPath);
            $data = json_decode($json, true);
            \acf_add_local_field_group($data);
        }
    }

    public function hasAcf(): bool
    {
        if (!$this->existsAcf()) return false;
        return !empty($this->acfItems);
    }

    public function existsAcf(): bool
    {
        return function_exists('acf_add_local_field_group');
    }
}
