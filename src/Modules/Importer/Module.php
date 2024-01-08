<?php

namespace Morpheus\Modules\Importer;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;
use Morpheus\Shared\Traits\UseConfig;

class Module implements ModuleInterface
{
    use Singleton, HasHooks, UseConfig;

    public function init(): void
    {
        new MorpheusSearch();

        $this->addFilter('wp_kses_allowed_html',    [$this, 'allowKsesCustomTags'], 10, 2);
        $this->registerHooks();
    }

    public function allowKsesCustomTags(array $tags): array
    {
        $tags['iframe'] = [
            'provider' => true,
            'src' => true,
            'width' => true,
            'height' => true,
            'frameborder' => true,
            'allowfullscreen' => true
        ];
        $tags['script'] = [
            'src' => true,
            'async' => true,
            'charSet' => true,
            'crossOrigin' => true,
            'defer' => true,
            'integrity' => true,
            'noModule' => true,
            'referrerPolicy' => true,
            'type' => true
        ];
        return $tags;
    }
}
