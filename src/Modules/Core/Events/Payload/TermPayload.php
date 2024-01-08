<?php

namespace Morpheus\Modules\Core\Events\Payload;

use Morpheus\Contracts\Exportable;
use Morpheus\Modules\Core\Classes\EventSEO;
use Morpheus\Modules\Core\Classes\Tracker\TrackerUuid;
use Morpheus\Shared\Helper;
use Morpheus\Shared\Traits\UseConfig;
use WP_Term;

class TermPayload implements Exportable
{
    use UseConfig;

    private WP_Term $term;

    public function __construct(WP_Term $term)
    {
        $this->term = $term;
    }

    public function export()
    {
        return $this->parse();
    }

    private function parse(): array
    {
        $seo = new EventSEO($this->term->term_id, true);
        return [
            'type'              => 'term',
            'config'            => $this->getConfigs(),
            'payload'           => [
                'site'              => $this->getConfig('domain_id'),
                'id'                => $this->term->term_id,
                'uuid'              => TrackerUuid::getFromObject($this->term),
                'parent'            => $this->term->parent,
                'title'             => $this->term->name,
                'slug'              => $this->term->slug,
                'taxonomy'          => $this->term->taxonomy,
                'uri'               => Helper::getRelativeTermLink($this->term),
                'description'       => $this->term->description,
                'meta'              => $this->getFields($this->term) ?: [],
                'seo'               => $seo->getData(),
            ]
        ];
    }
}
