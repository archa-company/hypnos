<?php

namespace Morpheus\Modules\HomeEditor\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\HomeEditor\Events\EventHomeSaved;
use Morpheus\Modules\HomeEditor\Events\HomePayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use Morpheus\Shared\Helper;
use WP_Post;

class HomeOnSave implements Actionable
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

        $detail = new HomePayload($post);
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventHomeSaved($payload);
        $event->send();
    }
}
