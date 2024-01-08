<?php

namespace Morpheus\Modules\Core\Classes;

use Morpheus\Modules\Core\Classes\Tracker\TrackerUuid;
use Morpheus\Modules\Core\Events\EventPostSaved;
use Morpheus\Modules\Core\Events\Payload\PostPayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use Morpheus\Shared\Helper;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\UseRest;
use WP_Post;
use WP_REST_Request;

class Rest
{
    use HasHooks, UseRest;

    public function __construct()
    {
        $this->addRestRoute(namespace: 'morpheus/v1', route: '/hermes/json/post/(?P<id>[0-9-]+)', methods: \WP_REST_Server::READABLE, callback: [$this, 'getPost']);
        $this->addRestRoute(namespace: 'morpheus/v1', route: '/hermes/send/post/(?P<id>[0-9-]+)', methods: \WP_REST_Server::READABLE, callback: [$this, 'sendToHermes']);
        $this->registerRest();
    }

    public function sendToHermes(WP_REST_Request $request)
    {
        $postId = (int) $request->get_param('id');
        $post = get_post($postId);

        if (!in_array($post->post_status, ['publish'])) return;
        if (Helper::preventTwiceHook(__CLASS__, $postId, 10, false)) return;

        $detail = $this->getPostPayload($post);

        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventPostSaved($payload);
        $event->send();
    }

    public function getPost(WP_REST_Request $request)
    {
        $postId = (int) $request->get_param('id');
        $post = get_post($postId);
        $payload = $this->getPostPayload($post);
        return $payload->export();
    }

    private function getPostPayload(WP_Post $post)
    {
        TrackerUuid::getFromObject($post);
        return new PostPayload($post);
    }
}
