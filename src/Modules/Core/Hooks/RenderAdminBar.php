<?php

namespace Morpheus\Modules\Core\Hooks;

use Morpheus\Contracts\Actionable;

class RenderAdminBar implements Actionable
{
    public function __invoke(...$params): void
    {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
    }
}
