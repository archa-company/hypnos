<?php

namespace Morpheus\Modules\Core\Events\Payload;

use Morpheus\Contracts\Exportable;
use Morpheus\Modules\Core\Classes\EventSEO;
use Morpheus\Modules\Core\Classes\EventTaxonomies;
use Morpheus\Modules\Core\Classes\Tracker\TrackerUuid;
use Morpheus\Shared\Classes\ContentBlocks;
use Morpheus\Shared\Helper;
use Morpheus\Shared\Traits\UseConfig;
use WP_Post;

class PostPayload implements Exportable
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
        $blocks = ContentBlocks::factory()->getBlocks($this->post->post_content);
        $result = [
            'type'              => 'post',
            'config'            => $this->getConfigs(),
            'payload'           => [
                'site'              => $this->getConfig('domain_id'),
                'id'                => $this->post->ID,
                'uuid'              => TrackerUuid::get($this->post->ID),
                'type'              => $this->post->post_type,
                'status'            => $this->post->post_status,
                'slug'              => $this->post->post_name,
                'hat'               => get_post_meta($this->post->ID, 'sobretitulo', true),
                'title'             => $this->post->post_title,
                'uri'               => Helper::getRelativePermalink($this->post->ID),
                'description'       => $this->post->post_excerpt,
                'thumbnail'         => get_the_post_thumbnail_url($this->post->ID, 'full'),
                'thumbnailMeta'     => $this->getThumbnailMeta(),
                'createdAt'         => date('c', strtotime($this->post->post_date)),
                'updatedAt'         => date('c', strtotime($this->post->post_modified)),
                'seo'               => $seo->getData(),
                'primaryCategory'   => $taxonomies->getPrimaryTerm(),
                'taxonomies'        => $taxonomies->getData(),
                'contentJson'       => $blocks,
            ],
        ];

        // Se não houver blocks, exporta em HTML
        if (empty($blocks)) {
            $result['payload']['contentHtml'] = apply_filters('the_content', $this->post->post_content);
        }

        return $result;
    }

    private function getThumbnailMeta()
    {
        $thumbnailId = get_post_thumbnail_id($this->post->ID);
        return [
            'caption'           => get_the_post_thumbnail_caption($this->post->ID),
            'credit'            => get_post_meta($thumbnailId, 'image_credit', true),
        ];
    }
}
