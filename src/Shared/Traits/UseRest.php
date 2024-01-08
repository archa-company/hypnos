<?php

namespace Morpheus\Shared\Traits;

use Morpheus\Shared\Classes\Rest;

trait UseRest
{

    use HasHooks;

    private $restRoutes = [];

    public function registerRest()
    {
        $this->addAction('rest_api_init', [$this, 'registerRestCallback']);
        $this->registerHooks();
    }

    public function registerRestCallback()
    {
        if (!$this->hasRestRoutes()) return;
        foreach ($this->restRoutes as $rest) {
            register_rest_route($rest->namespace, $rest->route, [
                'methods' => $rest->methods,
                'callback' => $rest->callback,
                'permission_callback' => $rest->permission,
            ], $rest->override);
        }
    }

    public function addRestRoute(string $namespace = 'morpheus/v1', string $route, string $methods, $callback, $permission = '__return_true', bool $override = false)
    {
        $this->restRoutes[] = new Rest($namespace, $route, $methods, $callback, $permission, $override);
    }

    public function hasRestRoutes(): bool
    {
        return count($this->restRoutes) > 0;
    }
}
