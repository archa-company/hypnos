<?php

namespace Morpheus\Modules\PushNotification;

use Morpheus\Shared\Traits\UseConfig;

class DashboardWidget
{

    use UseConfig;

    public function __construct()
    {
        add_action('admin_enqueue_scripts',     [$this, 'enqueueAssets']);
        add_action('wp_dashboard_setup',        [$this, 'registerWidget']);
    }

    public function registerWidget()
    {
        if (!current_user_can('edit_pages')) return;
        wp_add_dashboard_widget('morpheus-push-message-widget', 'Enviar Push Notification', [$this, 'render']);
    }

    public function enqueueAssets($hook)
    {
        if (!current_user_can('edit_pages')) return;
        $screen = get_current_screen();
        if ('dashboard' !== $screen->id) return;
        $screen->is_block_editor(true);
        $assetsUrl = MORPHEUS_CORE_URL . "src/assets";
        // dd($assetsUrl);
        wp_register_script('morpheus-push-message-widget', $assetsUrl . '/js/admin/push.js', ['wp-blocks', 'wp-element', 'wp-components', 'wp-data', 'wp-compose'], '1.0', true);
        wp_enqueue_script('wp-edit-post');
        wp_enqueue_script('morpheus-push-message-widget');
        wp_enqueue_style('wp-components');
    }

    public function render()
    {
?>
        <style type="text/css">
            #morpheus-push-message-widget .inside {
                padding: 1rem;
                margin-top: 0px;
            }

            #morpheus-push-message-widget .components-notice {
                margin: -1rem;
                margin-bottom: 1rem;
            }

            #morpheus-push-message-widget .components-notice.is-dismissible {
                padding-right: 0;
            }

            #morpheus-push-message-widget .validate__error {
                color: #d94f4f !important;
            }

            #morpheus-push-message-widget .validate__error .components-text-control__input {
                border-color: #d94f4f !important;
            }

            #morpheus-push-message-widget .validate__success {
                color: #4ab866 !important;
            }

            #morpheus-push-message-widget .validate__success .components-text-control__input {
                border-color: #4ab866 !important;
            }

            #morpheus-push-message-widget .validate__warning {
                color: #f0b849 !important;
            }

            #morpheus-push-message-widget .validate__warning .components-text-control__input {
                border-color: #f0b849 !important;
            }

            #morpheus-push-message-widget .components-text-control__input {
                border-radius: 4px;
            }

            #morpheus-push-message-widget .schedule-text-control {
                display: flex;
                justify-content: space-between;
                margin-bottom: 1rem;
            }

            #morpheus-push-message-widget .actions-control {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
        </style>
        <div id="MorpheusPushMessageWidget"></div>
<?php
    }
}
