<?php

namespace Morpheus;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Shared\Traits\HasBlocks;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;

class Blocks implements ModuleInterface
{
    use Singleton, HasHooks, HasBlocks;

    public string $assetsBaseUrl;

    public function __construct()
    {
        $this->assetsBaseUrl = get_site_url() . '/wp-content/plugins/morpheus/src';
        $this->init();
    }

    public function init(): void
    {
        $this->addFilter('block_type_metadata_settings', [$this, 'typeSettingsIncludeMorpheus'], 10, 2);
        $this->addFilter('block_categories_all',        [$this, 'registerCategories'], 10, 2);
        $this->addAction('init',                        [$this, 'registerBlocks']);
        $this->registerHooks();
    }

    public function registerCategories($categories, $context): array
    {
        return array_merge(
            [
                [
                    'slug' => 'morpheus',
                    'title' => 'Morpheus',
                    'icon'  => 'heart',
                ],
            ],
            $categories
        );
    }

    public function typeSettingsIncludeMorpheus(array $settings, array $metadata): array
    {
        if (!empty($metadata['morpheus']) && is_array($metadata['morpheus'])) {
            $settings['morpheus'] = (object) $metadata['morpheus'];
        }
        return $settings;
    }
}
