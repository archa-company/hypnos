<?php

namespace Morpheus\Modules\WebStories\Events;

use Morpheus\Contracts\Exportable;
use Morpheus\Modules\Core\Classes\EventSEO;
use Morpheus\Modules\Core\Classes\EventTaxonomies;
use Morpheus\Modules\Core\Classes\Tracker\TrackerUuid;
use Morpheus\Shared\Dev;
use Morpheus\Shared\Helper;
use Morpheus\Shared\Traits\UseConfig;
use WP_Post;

class StoryPayload implements Exportable
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
        $seo = new EventSEO($this->post->ID);
        $taxonomies = new EventTaxonomies($this->post);
        return [
            'type'              => 'story',
            'config'            => $this->getConfigs(),
            'payload'           => [
                'site'              => $this->getConfig('domain_id'),
                'type'              => $this->post->post_type,
                'id'                => $this->post->ID,
                'uuid'              => TrackerUuid::get($this->post->ID),
                'status'            => $this->post->post_status,
                'slug'              => $this->post->post_name,
                'title'             => $this->post->post_title,
                'uri'               => $this->getUrl(),
                'description'       => $this->post->post_excerpt,
                'thumbnail'         => get_the_post_thumbnail_url($this->post->ID, 'full') ?: null,
                'createdAt'         => date('c', strtotime($this->post->post_date)),
                'updatedAt'         => date('c', strtotime($this->post->post_modified)),
                'seo'               => $seo->getData(),
                'primaryCategory'   => $taxonomies->getPrimaryTerm(),
                'taxonomies'        => $taxonomies->getData(),
                // 'html'              => Helper::getContentHtmlById($this->post->ID),
            ],
        ];
    }

    private function getUrl(): string
    {
        $url = Helper::getRelativePermalink($this->post->ID);
        return str_replace('web-stories', 'stories', $url);
    }
}
