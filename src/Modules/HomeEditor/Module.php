<?php

namespace Morpheus\Modules\HomeEditor;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Modules\HomeEditor\Hooks\AdminMenus;
use Morpheus\Modules\HomeEditor\Hooks\EnqueueScripts;
use Morpheus\Modules\HomeEditor\Hooks\HomeOnSave;
use Morpheus\Modules\HomeEditor\Hooks\HomeSaveOnWPBlock;
use Morpheus\Modules\HomeEditor\Hooks\HomeSetTitle;
use Morpheus\Modules\HomeEditor\Hooks\RegisterPostType;
use Morpheus\Modules\HomeEditor\Hooks\YoastRemoveMetabox;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;
use Morpheus\Shared\Traits\UseConfig;

class Module implements ModuleInterface
{
    use Singleton, HasHooks, UseConfig;

    public function init(): void
    {
        if (!$this->getConfigRaw('features_home_editor')) return;

        $this->addAction('init',                    new RegisterPostType);
        $this->addAction('admin_menu',              new AdminMenus);
        $this->addAction('save_post_home',          new HomeOnSave, 9999990, 3);
        $this->addAction('save_post_wp_block',      new HomeSaveOnWPBlock, 9999990, 3);
        $this->addAction('add_meta_boxes',          new YoastRemoveMetabox, 100);
        // $this->addAction('admin_enqueue_scripts',   new EnqueueScripts);
        // $this->addFilter('wp_insert_post_data',     new HomeSetTitle, 1, 2);
        $this->registerHooks();
    }
}
