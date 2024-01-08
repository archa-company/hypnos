<?php

namespace Morpheus\Modules\Core\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\Core\Events\EventPostRedirect;
use Morpheus\Modules\Core\Events\Payload\RedirectPayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use Morpheus\Shared\Helper;
use WP_Post;

class PostUpdated implements Actionable
{
    public function __invoke(...$params): void
    {
        /**
         * @var int $postId
         * @var WP_Post $post
         * @var WP_Post $postOld
         */
        [$postId, $post, $postOld] = $params;

        if (!in_array($post->post_type, ['post', 'page'])) return;
        if (!in_array($post->post_status, ['publish'])) return;
        if (!in_array($postOld->post_status, ['publish'])) return;
        if (Helper::preventTwiceHook(__CLASS__, $postId)) return;
        if (get_permalink($post) === get_permalink($postOld)) return;

        $detail = new RedirectPayload($post, $postOld);
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventPostRedirect($payload);
        $event->send();
    }
}
