<?php

namespace Morpheus\Modules\Core\Classes;

use Morpheus\Shared\Classes\ContentBlocks;
use Morpheus\Shared\Traits\HasHooks;
use WP_REST_Request;

class Rest
{
    use HasHooks;

    public function __construct()
    {
        $this->addAction('rest_api_init', [$this, 'registerRoutes']);
        $this->registerHooks();
    }

    public function registerRoutes()
    {
        register_rest_route('morpheus/v1', '/json/post/(?P<id>[0-9-]+)', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'getPost'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function getPost(WP_REST_Request $request)
    {
        $post = get_post($request->get_param('id'));
        setup_postdata($post);

        $yoast = YoastSEO()->meta->for_post($post->ID);

        $postData = array(
            'id' => $post->ID,
            'type' => $post->post_type,
            'slug' => $post->post_name,
            'headline' => $post->post_title,
            'description' => $post->post_excerpt,
            'datePublished' => date('c', strtotime($post->post_date)),
            'dateModified' => date('c', strtotime($post->post_modified)),
            'url' => str_replace(get_site_url(), '', get_permalink($post->ID)),
            'status' => $post->post_status,
            'comments' => $post->comment_status,
            'author' => array(
                'name' => get_the_author_meta('display_name', $post->post_author),
                'slug' => get_the_author_meta('user_nicename', $post->post_author)
            ),
            'blocks' => ContentBlocks::factory()->getBlocks($post->post_content),
            'html' => apply_filters('the_content', $post->post_content),
            // 'breadcrumbs' => self::breadcrumb(get_permalink($post->ID)),
            'taxonomies' => $this->taxonomies($post->ID),
            'seo' => array(
                'title' => $yoast->title,
                'description' => $yoast->meta_description ?: $yoast->description,
                'canonical' => $yoast->canonical,
                // 'redirect' => get_post_meta($post->ID, '_yoast_wpseo_redirect', true),
            ),
            'social' => array(
                'og' => array(
                    'publisher' => $yoast->open_graph_publisher,
                    'type' => $yoast->open_graph_type,
                    'site_name' => $yoast->open_graph_site_name,
                    'site_url' => $yoast->open_graph_url,
                    'title' => $yoast->open_graph_title ?: $yoast->title,
                    'description' => $yoast->open_graph_description ?: $yoast->description,
                    'images' => $yoast->open_graph_images,
                    'locale' => $yoast->open_graph_locale,
                    'article_publisher' => $yoast->open_graph_article_publisher,
                    'article_author' => $yoast->open_graph_article_author,
                    'article_published_time' => $yoast->open_graph_article_published_time,
                    'article_modified_time' => $yoast->open_graph_article_modified_time,
                ),
                'twitter' => array(
                    'card' => $yoast->twitter_card,
                    'title' => $yoast->twitter_title ?: $yoast->title,
                    'description' => $yoast->twitter_description ?: $yoast->description,
                    'image' => $yoast->twitter_image ?: $yoast->open_graph_images,
                    'creator' => $yoast->twitter_creator,
                    'site' => $yoast->twitter_site,
                )
            ),
            'image' => $this->image($post->ID)
        );

        return $postData;
    }

    public function taxonomies($post_id)
    {
        $taxonomies = [];
        $categories = wp_get_post_terms($post_id, 'category', ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all']);
        $postTags = wp_get_post_terms($post_id, 'post_tag', ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all']);

        foreach ($categories as $category) {
            $ancestors = get_ancestors($category->term_id, 'category', 'taxonomy');
            foreach ($ancestors as $parent) {
                if (array_search($parent, array_column($categories, 'term_id')) === FALSE) {
                    array_unshift($categories, get_term($parent, 'category'));
                }
            }
        }

        foreach ($categories as $term) {
            $taxonomies['category'][] = [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'url' => str_replace(get_site_url(), '', get_term_link($term)),
                'description' => $term->description
            ];
        }

        foreach ($postTags as $term) {
            $taxonomies['tags'][] = [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'url' => str_replace(get_site_url(), '', get_term_link($term)),
                'description' => $term->description
            ];
        }

        return $taxonomies;
    }

    public function image($post_id)
    {
        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        if (empty($post_thumbnail_id)) return;

        $post_thumbnail = get_post($post_thumbnail_id);
        $attachment_metadata = wp_get_attachment_metadata($post_thumbnail_id, true);
        list($url, $width, $height, $is_intermediate) = wp_get_attachment_image_src($post_thumbnail_id, 'large');

        $image = array(
            'url' => str_replace(wp_upload_dir()['baseurl'], '', $url),
            'title' => $post_thumbnail->post_title,
            'caption' => $post_thumbnail->post_excerpt,
            'description' => $post_thumbnail->post_content,
            'width' => $width,
            'height' => $height,
            'sizes' => array()
        );

        // $dir = '/' . substr($attachment_metadata['file'], 0, strrpos($attachment_metadata['file'], '/')) . '/';

        foreach ($attachment_metadata['sizes'] as $size => $metas) {
            list($url, $width, $height, $is_intermediate) = wp_get_attachment_image_src($post_thumbnail_id, $size);
            $metas['file'] = $url;
            $image['sizes'][$size] = $metas;
        }

        return $image;
    }
}
