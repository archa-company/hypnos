<?php

namespace Morpheus\Modules\WebStories\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\WebStories\Events\EventStoryRemoved;
use Morpheus\Modules\Core\Events\Payload\RemovePayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use Morpheus\Shared\Helper;
use WP_Post;

class StoryRemoved implements Actionable
{
    public function __invoke(...$params): void
    {
        /**
         * @var int $postId
         * @var WP_Post $post
         * @var WP_Post $postOld
         */
        [$postId, $post, $postOld] = $params;

        if ($post->post_type !== 'web-story') return;
        if (!in_array($post->post_status, ['draft', 'trash'])) return;
        if (!in_array($postOld->post_status, ['publish'])) return;
        if (Helper::preventTwiceHook(__CLASS__, $postId)) return;

        $detail = new RemovePayload($post, $postOld);
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventStoryRemoved($payload);
        $event->send();
    }
}
