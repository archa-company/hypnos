<?php

namespace Morpheus\Modules\Cleaning;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;
use Morpheus\Shared\Traits\UseConfig;

class Module implements ModuleInterface
{
    use Singleton, HasHooks, UseConfig;

    public function init(): void
    {
        if (!$this->getConfigRaw('features_db_cleaning')) {
            wp_clear_scheduled_hook('morpheus_cleaning_published_posts');
            return;
        }
        if (!wp_next_scheduled('morpheus_cleaning_published_posts')) {
            wp_schedule_event(time(), 'daily', 'morpheus_cleaning_published_posts');
        }
    }
}

function morpheus_cleaning_published_posts()
{
    if (!get_field('features_db_cleaning', 'options', true)) return;
    $days = get_field('cleaning_days', 'options', true) ?: 60;

    global $wpdb;
    $sql = $wpdb->prepare("SELECT ID
    FROM {$wpdb->posts}
    WHERE post_type = 'post'
    AND post_status = 'publish'
    AND post_date < now() - interval %d DAY
    ORDER BY ID DESC
    LIMIT 10", $days);

    $posts = $wpdb->get_col($sql);

    if (!$posts) return;

    foreach ($posts as $postId) {
        wp_delete_post($postId, true);
    }
}
