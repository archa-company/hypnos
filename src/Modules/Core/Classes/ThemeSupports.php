<?php

namespace Morpheus\Modules\Core\Classes;

use Morpheus\Shared\Traits\HasHooks;

class ThemeSupports
{

    use HasHooks;

    public function __construct()
    {
        $this->addAction('init',                            [$this, 'registerMenus']);
        $this->addAction('after_setup_theme',               [$this, 'registerSupports'], 0);
        $this->addAction('enqueue_block_editor_assets',     [$this, 'blockEditorStyles'], 1, 1);
        $this->registerHooks();
    }

    public function registerSupports(): void
    {
        // Set content-width.
        global $content_width;
        if (!isset($content_width)) {
            $content_width = 780;
        }

        add_theme_support('align-wide');
        add_theme_support('responsive-embeds');
        add_theme_support('post-thumbnails');

        // Set post thumbnail size.
        set_post_thumbnail_size(1200, 9999);
        // Add custom image size used in Cover Template.
        add_image_size('fullscreen', 1980, 9999);

        // Custom logo.
        $logo_width  = 120;
        $logo_height = 90;
        // If the retina setting is active, double the recommended width and height.
        if (get_theme_mod('retina_logo', false)) {
            $logo_width  = floor($logo_width * 2);
            $logo_height = floor($logo_height * 2);
        }
        add_theme_support(
            'custom-logo',
            array(
                'height'      => $logo_height,
                'width'       => $logo_width,
                'flex-height' => true,
                'flex-width'  => true,
            )
        );
    }

    public function registerMenus(): void
    {
        $locations = array(
            'primary'  => 'Menu Principal',
            'mobile'   => 'Menu Mobile',
            'footer'   => 'Menu Footer',
        );
        register_nav_menus($locations);
    }

    public function blockEditorStyles()
    {
        // Enqueue the editor styles.
        wp_enqueue_style('morpheus-block-editor-styles', get_theme_file_uri('/editor-style-block.css'), array(), wp_get_theme()->get('Version'), 'all');
    }
}
