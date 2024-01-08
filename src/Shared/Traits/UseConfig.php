<?php

namespace Morpheus\Shared\Traits;

use Morpheus\Shared\Helper;

trait UseConfig
{

    public function getConfigs(bool $objectFormat = false)
    {
        $fields = $this->getConfigFields();
        $config = $this->parseConfigs($fields);

        $config['domain']['cms'] = Helper::removeProtocol(get_site_url());
        $config['infra'] = $config['hermes'];
        unset($config['hermes']);
        ksort($config);

        return ($objectFormat) ? (object) $config : $config;
    }

    private function parseConfigs(array $fields)
    {
        $configs = [];
        foreach ($fields as $field => $value) {
            preg_match("/^(?<group>[^_]+)_(?<key>.*)$/", $field, $keys, PREG_UNMATCHED_AS_NULL);
            if (!$keys) continue;
            $key = Helper::toCamelCase($keys['key']);
            $configs[$keys['group']][$key] = $value;
        }
        return $configs;
    }

    public function getConfigFields(bool $formated = true): array
    {
        return get_fields('options', $formated) ?: [];
    }

    public function getConfig(string $name, bool $formated = true, $defaultValue = '')
    {
        return get_field($name, 'options', $formated) ?: $defaultValue;
    }

    public function getConfigRaw(string $name, $defaultValue = '')
    {
        return $this->getOption("options_{$name}", $defaultValue);
    }

    public function getFields($key, bool $formated = true): array
    {
        return get_fields($key, $formated) ?: [];
    }

    public function getConfigEventbus($onPreAcfInit = true): string
    {
        $key = 'hermes_eventbus';
        return ($onPreAcfInit)
            ? $this->getConfigRaw($key)
            : $this->getConfig($key);
    }

    public function hasConfigEventbus($onPreAcfInit = true): bool
    {
        if (empty($this->getConfigEventbus($onPreAcfInit))) return false;
        return true;
    }

    public function getOption(string $name, $defaultValue = '')
    {
        return get_option($name, $defaultValue);
    }
}
