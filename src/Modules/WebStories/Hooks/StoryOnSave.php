<?php

namespace Morpheus\Modules\WebStories\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\Core\Classes\Tracker\TrackerUuid;
use Morpheus\Modules\WebStories\Events\EventStorySaved;
use Morpheus\Modules\WebStories\Events\StoryPayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use Morpheus\Shared\Helper;
use WP_Post;

class StoryOnSave implements Actionable
{
    public function __invoke(...$params): void
    {
        /**
         * @var int $postId
         * @var WP_Post $post
         * @var bool $updated
         */
        [$postId, $post, $updated] = $params;

        if ($post->post_status !== 'publish') return;
        if (Helper::preventTwiceHook(__CLASS__, $postId)) return;

        // Gera o UUID do Post e salva em post_meta
        if (!$updated) TrackerUuid::setFromObject($post);

        $detail = new StoryPayload($post);
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventStorySaved($payload);
        $event->send();
    }
}
