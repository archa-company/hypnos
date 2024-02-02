<?php

namespace Morpheus\Modules\PushNotification;

use Morpheus\Modules\PushNotification\ActionController;

class JsonRest
{
    public function __construct()
    {
        if (get_current_blog_id() !== 1) return;
        add_action('rest_api_init',         [$this, 'registerRoutes']);
    }

    /**
     * Registra uma nova rota na API para retornar todos os menus
     */
    public function registerRoutes()
    {
        if (!current_user_can('edit_pages')) return;

        /**
         * Importa todos os providers
         */
        $action = new ActionController;
        register_rest_route('morpheus/v1', '/push-message', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => $action,
            'permission_callback' => '__return_true',
            // 'permission_callback' => function() {
            //     return current_user_can('edit_pages');
            // }
        ]);
    }
}
