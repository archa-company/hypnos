<?php

namespace Morpheus\Modules\WebStories\Events;

use Morpheus\Contracts\Exportable;
use Morpheus\Shared\Helper;
use Morpheus\Shared\Traits\UseConfig;
use WP_Post;

class StoryRemovedPayload implements Exportable
{

    use UseConfig;

    private WP_Post $post;
    private WP_Post $oldPost;

    public function __construct(WP_Post $post, WP_Post $oldPost)
    {
        $this->post = $post;
        $this->oldPost = $oldPost;
    }

    public function export()
    {
        return $this->parse();
    }

    private function parse(): array
    {
        return [
            'type'              => 'story',
            'config'            => $this->getConfigs(),
            'payload'           => [
                'site'              => $this->getConfig('domain_id'),
                'type'              => $this->post->post_type,
                'id'                => $this->post->ID,
                'status'            => $this->post->post_status,
                'slug'              => $this->post->post_name,
                'uri'               => $this->getUrl(),
            ],
        ];
    }

    private function getUrl(): string
    {
        $url = Helper::getRelativePermalink($this->oldPost->ID);
        return str_replace('web-stories', 'stories', $url);
    }
}
