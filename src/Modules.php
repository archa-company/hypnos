<?php

namespace Morpheus;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Shared\Traits\Singleton;

class Modules implements ModuleInterface
{
    use Singleton;

    public $modules = [];

    public function init(): void
    {
        $this->registerModules();
    }

    public function registerModules()
    {
        $moduleClasses    = require_once __DIR__ . '/register.php';
        if (empty($moduleClasses)) return;

        foreach ($moduleClasses as $name => $class) {
            $this->modules[$name] = $class::getInstance();
        }
    }

    public function activation()
    {
        // $this->auth->registerCapabilities();
        flush_rewrite_rules();
    }

    public function deactivation()
    {
        // $this->auth->unregisterCapabilities();
        flush_rewrite_rules();
    }
}
