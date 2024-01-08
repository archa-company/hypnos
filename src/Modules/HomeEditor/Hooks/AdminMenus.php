<?php

namespace Morpheus\Modules\HomeEditor\Hooks;

use Morpheus\Contracts\Actionable;

class AdminMenus implements Actionable
{
    public function __invoke(...$params): void
    {
        /**
         * Crar um menu de Diagramação com a administração de Blocos
         */
        add_menu_page('edit.php?post_type=wp_block', 'Diagramação', 'publish_pages', 'edit.php?post_type=wp_block', '', 'dashicons-layout', 2);
        add_submenu_page('edit.php?post_type=wp_block', 'Blocos', 'Todos os Blocos', 'publish_pages', 'edit.php?post_type=wp_block');

        /**
         * Altera a ordem dos submenus
         */
        global $submenu;
        if (isset($submenu['edit.php?post_type=wp_block'])) {
            array_unshift($submenu['edit.php?post_type=wp_block'], max($submenu['edit.php?post_type=wp_block']));
            $max = array_keys($submenu['edit.php?post_type=wp_block'], max($submenu['edit.php?post_type=wp_block']));
            unset($submenu['edit.php?post_type=wp_block'][$max[1]]);
        }
    }
}
