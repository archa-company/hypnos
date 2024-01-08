<?php

namespace Morpheus\Modules\CDN\Hooks;

use Morpheus\Contracts\Actionable;

class AdminMenu implements Actionable
{
    public function __invoke(...$params): void
    {
        $capability = (is_multisite()) ? 'manage_sites' : 'manage_options';
        add_submenu_page('morpheus', 'CDN Invalidations', 'CDN Cache', $capability, 'morpheus-cdn', [$this, 'cdn']);
    }

    public function cdn()
    {
        require_once __DIR__ . '/../Pages/' . __FUNCTION__ . '.php';
    }
}
