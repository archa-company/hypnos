<?php

namespace Morpheus\Modules\HomeEditor\Hooks;

use Morpheus\Contracts\Actionable;

class EnqueueScripts implements Actionable
{
    public function __invoke(...$params): void
    {
        [$hook] = $params;
        global $post_type;
        if (!in_array($post_type, ['home', 'wp_block']) || $hook != 'post.php') return;
        $homeUrl = get_home_url();
        wp_enqueue_style('home-admin', "{$homeUrl}/resources/css/home-admin.css", [], 'v1', 'all');
    }
}
