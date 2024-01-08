<?php

namespace Morpheus\Modules\Core\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\Core\Classes\Tracker\TrackerUuid;
use Morpheus\Modules\Core\Events\EventTermSaved;
use Morpheus\Modules\Core\Events\Payload\TermPayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use WP_Post;

class TermOnSave implements Actionable
{
    public function __invoke(...$params): void
    {
        /**
         * @var int $postId
         * @var WP_Post $post
         * @var bool $updated
         */
        [$termId, $ttId, $taxonomy, $updated, $args] = $params;

        if (!in_array($taxonomy, ['category', 'post_tag', 'credit'])) return;

        $term = get_term($termId, $taxonomy);

        // Gera o UUID do Post e salva em post_meta
        if (!$updated) TrackerUuid::setFromObject($term);

        $detail = new TermPayload($term);
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventTermSaved($payload);
        $event->send();
    }
}
