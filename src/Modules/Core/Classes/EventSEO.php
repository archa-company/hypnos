<?php

namespace Morpheus\Modules\Core\Classes;

use Yoast\WP\SEO\Surfaces\Values\Meta;

class EventSEO
{
    public Meta|false $yoast;

    public function __construct(int $id, $withTerm = false)
    {
        $this->yoast = ($withTerm)
            ? YoastSEO()->meta->for_term($id)
            : YoastSEO()->meta->for_post($id);
    }

    /**
     * @see https://developer.yoast.com/customization/apis/surfaces-api/
     */
    public function __get($name)
    {
        return $this->yoast->$name;
    }

    public function getHead()
    {
        return $this->yoast->get_head()->json;
    }

    public function getData()
    {
        $description = $this->meta_description ?: $this->open_graph_description;
        $image = $this->open_graph_image ? current($this->open_graph_image)['url'] : null;
        return (object) [
            'title' => $this->title,
            'description' => $description,
            // 'canonical' => $this->canonical,
            // 'estimated_reading_time_minutes' => $this->estimated_reading_time_minutes,
            'open_graph' => [
                'type' => $this->open_graph_type,
                // 'url' => $this->open_graph_url,
                'title' => $this->open_graph_title,
                'description' => $this->open_graph_description,
                'image' => $image,
                'site_name' => $this->open_graph_site_name,
                'article_publisher' => $this->open_graph_article_publisher,
                'article_author' => $this->open_graph_article_author,
                'article_published_time' => $this->open_graph_article_published_time,
                'article_modified_time' => $this->open_graph_article_modified_time,
                'locale' => $this->open_graph_locale,
                'fb_app_id' => $this->open_graph_fb_app_id,
            ],
            'twitter' => [
                'card' => $this->twitter_card,
                'title' => $this->twitter_title ?: $this->title,
                'description' => $this->twitter_description ?: $description,
                'image' => $this->twitter_image ?: $image,
                'creator' => $this->twitter_creator,
                'site' => $this->twitter_site,
            ],
            'robots' => array_merge($this->robots, [
                'googlebot' => $this->googlebot,
                'bingbot' => $this->bingbot,
            ]),
        ];
    }
}
