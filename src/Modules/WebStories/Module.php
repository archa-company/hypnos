<?php

namespace Morpheus\Modules\WebStories;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Modules\WebStories\Classes\Analytics;
use Morpheus\Modules\WebStories\Hooks\StoryOnSave;
use Morpheus\Modules\WebStories\Hooks\StoryRemoved;
use Morpheus\Shared\Dev;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;
use Morpheus\Shared\Traits\UseConfig;

use function Google\Web_Stories\get_plugin_instance;

class Module implements ModuleInterface
{
    use Singleton, HasHooks, UseConfig;

    public function init(): void
    {
        if (!$this->getConfigRaw('features_web_stories')) return;

        new Analytics;

        $this->addAction('save_post_web-story',             new StoryOnSave, 9999992, 3, true);
        $this->addAction('post_updated',                    new StoryRemoved, 9999993, 3, true);

        $this->addAction('wp',                              [$this, 'removeCanonical']);
        $this->addFilter('wp_sitemaps_enabled',             '__return_false');
        $this->addFilter('xmlrpc_enabled',                  '__return_false');
        $this->addFilter('disable_wpseo_json_ld_search',    '__return_true');
        $this->addFilter('wpseo_json_ld_output',            '__return_false');

        $this->addAction('wp_loaded',                       [$this, 'registerSupports'], 9);
        $this->addAction('wp_loaded',                       [&$this, 'removeStoriesHead']);
        $this->addAction('wp_enqueue_scripts',              [$this, 'removeBlockStypes'], 100);

        $this->registerHooks();
    }

    public function removeCanonical(): void
    {
        if (!is_singular('web-story')) return;
        add_filter('wpseo_canonical', '__return_false');
    }

    public function removeBlockStypes()
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
    }

    public function removeStoriesHead()
    {
        $stories = get_plugin_instance();
        $container = $stories->get_container();
        $discovery = $container->get('discovery');
        // dd($discovery);

        // global $web_stories;
        remove_action('web_stories_story_head', 'rsd_link');
        remove_action('web_stories_story_head', 'wlwmanifest_link');
        remove_action('web_stories_story_head', 'wp_generator');
        remove_action('web_stories_story_head', 'adjacent_posts_rel_link');
        remove_action('web_stories_story_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        remove_action('web_stories_story_head', 'wp_shortlink_wp_head', 10, 0);
        remove_action('web_stories_story_head', 'feed_links', 2);
        remove_action('web_stories_story_head', 'feed_links_extra', 3);
        remove_action('web_stories_story_head', 'rest_output_link_wp_head', 10, 0);
        remove_action('web_stories_story_head', 'wp_oembed_add_discovery_links');
        // remove_action('web_stories_story_head', [$discovery, 'print_document_title']);
        // remove_action('web_stories_story_head', [$discovery, 'print_metadata']);
        remove_action('web_stories_story_head', [$discovery, 'print_schemaorg_metadata']);
        // remove_action('web_stories_story_head', [$discovery, 'print_open_graph_metadata']);
        // remove_action('web_stories_story_head', [$discovery, 'print_twitter_metadata']);

        remove_action('web_stories_story_head', [$discovery, 'print_feed_link'], 4);
        remove_action('wp_head', [$discovery, 'print_feed_link'], 4);

        // remove_action('web_stories_story_head', 'rel_canonical');
        // remove_action('web_stories_story_head', 'wp_site_icon', 99);
        // remove_action('web_stories_story_head', 'wp_robots', 1);
    }

    public function registerSupports()
    {
        remove_theme_support('automatic-feed-links');
        show_admin_bar(false);
    }
}
