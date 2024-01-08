<?php

namespace Morpheus\Modules\Core\Hooks;

use Morpheus\Contracts\Actionable;

class RegisterConfigPage implements Actionable
{
    public function __invoke(...$params): void
    {
        if (!function_exists('acf_add_options_page')) return;
        $capability = (is_multisite()) ? 'manage_sites' : 'manage_options';
        acf_add_options_page([
            'page_title'        => 'Morpheus: Core Settings',
            'menu_title'        => 'Morpheus',
            'menu_slug'         => 'morpheus',
            'position'          => 2,
            'icon_url'          => 'dashicons-games',
            'update_button'     => 'Salvar',
            'updated_message'   => 'Dados atualizados com sucesso!',
            'post_id'           => 'options',
            'capability'        => $capability,
            'redirect'          => false,
            'autoload'          => true,
        ]);
    }
}
