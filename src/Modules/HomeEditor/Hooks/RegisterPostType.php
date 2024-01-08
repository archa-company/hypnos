<?php

namespace Morpheus\Modules\HomeEditor\Hooks;

use Morpheus\Contracts\Actionable;

class RegisterPostType implements Actionable
{
    public function __invoke(...$params): void
    {
        register_post_type('home', [
            'label'                 => 'Home',
            'labels'                => [
                'name'                  => 'Homes',
                'singular_name'         => 'Home'
            ],
            'description'           => 'Homes do site',
            'supports'              => ['title', 'editor'],
            'taxonomies'            => [],
            'public'                => true,
            'show_ui'               => true,
            'show_in_rest'          => true,
            'show_in_menu'          => 'edit.php?post_type=wp_block',
            'show_in_nav_menus'     => false,
            'show_in_admin_bar'     => false,
            'menu_position'         => 4,
            'menu_icon'             => 'dashicons-exerpt-view',
            'can_export'            => true,
            'has_archive'           => false,
            'hierarchical'          => false,
            'rewrite'               => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        ]);
    }
}
