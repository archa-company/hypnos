<?php

namespace Morpheus\Modules\HomeEditor\Events;

use Morpheus\Contracts\Exportable;
use Morpheus\Modules\Core\Classes\EventSEO;
use Morpheus\Shared\Classes\ContentBlocks;
use Morpheus\Shared\Helper;
use Morpheus\Shared\Traits\UseConfig;
use WP_Post;

class HomePayload implements Exportable
{
    use UseConfig;

    private WP_Post $post;

    public function __construct(WP_Post $post)
    {
        $this->post = $post;
    }

    public function export()
    {
        return $this->parse();
    }

    private function parse(): array
    {
        $blocks = new ContentBlocks;
        $seo = new EventSEO($this->post->ID);
        return [
            'type'              => 'home',
            'config'            => $this->getConfigs(),
            'payload'           => [
                'site'              => $this->getConfig('domain_id'),
                'id'                => $this->post->ID,
                'type'              => $this->post->post_type,
                'status'            => $this->post->post_status,
                'slug'              => $this->post->post_name,
                'title'             => $this->post->post_title,
                'uri'               => $this->getUrl(),
                'description'       => $this->post->post_excerpt,
                'createdAt'         => date('c', strtotime($this->post->post_date)),
                'updatedAt'         => date('c', strtotime($this->post->post_modified)),
                'thumbnail'         => get_the_post_thumbnail_url($this->post->ID, 'full'),
                'seo'               => $seo->getData(),
                'contentJson'       => $blocks->getBlocks($this->post->post_content),
                'contentHtml'       => apply_filters('the_content', $this->post->post_content),
            ],
        ];
    }

    private function getUrl(): string
    {
        $url = Helper::getRelativePermalink($this->post->ID);
        return preg_replace("/^\/home/", '', $url);
    }
}
