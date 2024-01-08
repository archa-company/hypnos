<?php

namespace Morpheus\Modules\HomeEditor\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\HomeEditor\Events\EventHomeSaved;
use Morpheus\Modules\HomeEditor\Events\HomePayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use Morpheus\Shared\Helper;
use WP_Post;
use WP_Query;

class HomeSaveOnWPBlock implements Actionable
{
    public function __invoke(...$params): void
    {
        /**
         * @var int $postId
         * @var WP_Post $post
         * @var bool $updated
         */
        [$postId, $post, $updated] = $params;

        if (!in_array($post->post_status, ['publish'])) return;
        if (Helper::preventTwiceHook(__CLASS__, $postId)) return;

        $query = new WP_Query([
            'post_type'         => 'home',
            'post_name'         => 'home',
            'posts_per_page'    => 1,
            'orderby'           => 'ID',
            'order'             => 'desc',
        ]);

        $detail = new HomePayload($query->post);
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventHomeSaved($payload);
        $event->send();
    }
}
