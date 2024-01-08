<?php

namespace Morpheus\Modules\Core\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\Core\Classes\Tracker\TrackerUuid;
use Morpheus\Modules\Core\Events\EventPostSaved;
use Morpheus\Modules\Core\Events\Payload\PostPayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use Morpheus\Shared\Helper;
use WP_Post;

class PostOnSave implements Actionable
{
    public function __invoke(...$params): void
    {
        /**
         * @var int $postId
         * @var WP_Post $post
         * @var bool $updated
         */
        [$postId, $post, $updated] = $params;

        if (!in_array($post->post_type, ['post', 'page'])) return;
        if (!in_array($post->post_status, ['publish'])) return;
        if (Helper::preventTwiceHook(__CLASS__, $postId, 10, !$updated)) return;

        // Gera o UUID do Post e salva em post_meta
        if (!$updated) TrackerUuid::setFromObject($post);

        $detail = new PostPayload($post);
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventPostSaved($payload);
        $event->send();
    }
}
