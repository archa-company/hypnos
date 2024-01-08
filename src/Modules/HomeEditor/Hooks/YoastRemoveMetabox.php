<?php

namespace Morpheus\Modules\HomeEditor\Hooks;

use Morpheus\Contracts\Actionable;

class YoastRemoveMetabox implements Actionable
{
    public function __invoke(...$params): void
    {
        remove_meta_box('wpseo_meta', 'home', 'normal');
    }
}
