<?php

namespace Morpheus\Modules\Core\Events\Payload;

use Morpheus\Contracts\Exportable;
use Morpheus\Modules\Core\Classes\EventSEO;
use Morpheus\Modules\Core\Classes\EventTaxonomies;
use Morpheus\Shared\Classes\ContentBlocks;
use Morpheus\Shared\Helper;
use Morpheus\Shared\Traits\UseConfig;
use WP_Post;

class RedirectPayload implements Exportable
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
            'type'              => 'redirect',
            'config'            => $this->getConfigs(),
            'payload'           => [
                'site'              => $this->getConfig('domain_id'),
                'type'              => $this->post->post_type,
                'id'                => $this->post->ID,
                'uri'               => Helper::getRelativePermalink($this->oldPost),
                'redirect'          => Helper::getRelativePermalink($this->post),
            ],
        ];
    }
}
