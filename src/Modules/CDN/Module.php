<?php

namespace Morpheus\Modules\CDN;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Modules\CDN\Hooks\AdminMenu;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;

class Module implements ModuleInterface
{
    use Singleton, HasHooks;

    public function init(): void
    {
        $this->addAction('admin_menu', new AdminMenu, 99);
        $this->registerHooks();
    }
}
