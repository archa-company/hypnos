<?php

namespace Morpheus\Modules\PushNotification;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Modules\PushNotification\Contracts\ProviderInterface;
use Morpheus\Modules\PushNotification\Providers\Notix\Provider as NotixProvider;
use Morpheus\Modules\PushNotification\Providers\OneSignal\Provider as OneSignalProvider;
use Morpheus\Modules\PushNotification\Providers\SendPulse\Provider as SendPulseProvider;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;
use Morpheus\Shared\Traits\UseConfig;

class Module implements ModuleInterface
{
    use Singleton, HasHooks, UseConfig;

    public string $providerName;
    public string $providerClass;
    public ProviderInterface $provider;
    public JsonRest $rest;
    public DashboardWidget $dashboard;

    const DEFAULT_PROVIDER = 'Notix';
    const PROVIDERS = [
        'OneSignal' => OneSignalProvider::class,
        'SendPulse' => SendPulseProvider::class,
        'Notix' => NotixProvider::class,
    ];

    public function init(): void
    {
        if (!$this->getConfig('push_enable')) return;

        $this->setProvider();

        $this->rest = new JsonRest;
        $this->dashboard = new DashboardWidget;

        $this->addAction('wp_head',                         [$this, 'getHeadScript'], 99);
        $this->addAction('enqueue_block_editor_assets',     [$this, 'registerPostEditorAssets'], 100);
        $this->registerHooks();
    }

    private function setProvider()
    {
        $this->providerName = $this->getConfig('push_provider');
        $this->providerClass = "Morpheus\\Modules\\PushNotification\\Providers\\{$this->providerName}\\Provider";
        $this->provider = new $this->providerClass;
    }

    /**
     * Script para o <head>
     * @return void
     */
    public function getHeadScript(): void
    {
        echo $this->provider->getHeadScript();
    }

    public function registerPostEditorAssets()
    {
        global $post_type;
        if ($post_type !== 'post') return;
        $assetsUrl = MORPHEUS_CORE_URL . "src/assets";
        wp_enqueue_script("morpheus-sidebar", $assetsUrl . '/js/admin/post-sidebar.js', ['wp-plugins', 'wp-edit-post', 'wp-blocks', 'wp-data', 'wp-compose', 'wp-element', 'wp-components'], '1.0', true);
        wp_enqueue_style("morpheus-sidebar", $assetsUrl . '/css/admin/post-sidebar.css', [], '1.0', 'all');
    }

    public static function getProviderClass(string $providerName): string
    {
        return self::PROVIDERS[$providerName];
    }

    public static function providerFactory(string $providerName): ProviderInterface
    {
        $providerClass = self::getProviderClass($providerName);
        return new $providerClass;
    }
}
