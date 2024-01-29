<?php

namespace Morpheus\Modules\Core;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Modules\Core\Classes\AmpAds;
use Morpheus\Modules\Core\Classes\AmpAnalytics;
use Morpheus\Modules\Core\Classes\Credits;
use Morpheus\Modules\Core\Classes\ElasticSearch\ElasticSearch;
use Morpheus\Modules\Core\Classes\Rest;
use Morpheus\Modules\Core\Classes\Tags;
use Morpheus\Modules\Core\Classes\ThemeSupports;
use Morpheus\Modules\Core\Hooks\CategoryOnSave;
use Morpheus\Modules\Core\Hooks\ConfigOnSave;
use Morpheus\Modules\Core\Hooks\RegisterConfigPage;
use Morpheus\Modules\Core\Hooks\RenderAdminBar;
use Morpheus\Modules\Core\Hooks\MenuOnSave;
use Morpheus\Modules\Core\Hooks\PostOnSave;
use Morpheus\Modules\Core\Hooks\PostUpdated;
use Morpheus\Modules\Core\Hooks\PostRemoved;
use Morpheus\Modules\Core\Hooks\TermOnSave;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;
use Morpheus\Shared\Traits\UseACF;

class Module implements ModuleInterface
{
    use Singleton, HasHooks, UseACF;

    public function init(): void
    {
        new ElasticSearch;
        AmpAnalytics::factory()->init();
        AmpAds::factory()->init();

        $this->addAction('save_post_post',                  new PostOnSave, 9999990, 3, true);
        $this->addAction('save_post_page',                  new PostOnSave, 9999990, 3, true);
        $this->addAction('post_updated',                    new PostUpdated, 9999991, 3, true);
        $this->addAction('post_updated',                    new PostRemoved, 9999992, 3, true);
        $this->addAction('save_post_nav_menu_item',         new MenuOnSave, 9999990, 3);
        $this->addAction('acf/save_post',                   new ConfigOnSave, 9999990);
        $this->addAction('saved_nav_menu',                  new MenuOnSave, 9999993, 3);
        $this->addAction('saved_term',                      new TermOnSave, 9999993, 5);

        $this->addAction('acf/init',                        new RegisterConfigPage);
        $this->addAction('wp_before_admin_bar_render',      new RenderAdminBar);

        $this->addFilter('wp_sitemaps_enabled',             '__return_false');
        $this->addFilter('wpseo_json_ld_output',            '__return_false');
        $this->addFilter('disable_wpseo_json_ld_search',    '__return_true');

        new ThemeSupports;
        new Tags;
        new Credits;
        new Rest;

        $this->removeAction('template_redirect',            'rest_output_link_header', 11, 0);
        $this->removeAction('wp_head',                      'rsd_link');
        $this->removeAction('wp_head',                      'wlwmanifest_link');
        $this->removeAction('wp_head',                      'wp_generator');
        $this->removeAction('wp_head',                      'start_post_rel_link');
        $this->removeAction('wp_head',                      'index_rel_link');
        $this->removeAction('wp_head',                      'adjacent_posts_rel_link');
        $this->removeAction('wp_head',                      'adjacent_posts_rel_link_wp_head');
        $this->removeAction('wp_head',                      'wp_shortlink_wp_head');
        $this->removeAction('wp_head',                      'feed_links', 2);
        $this->removeAction('wp_head',                      'feed_links_extra', 3);
        $this->removeAction('wp_head',                      'rest_output_link_wp_head', 10);
        $this->removeAction('wp_head',                      'wp_oembed_add_discovery_links', 1);

        $this->registerHooks();

        $this->addAcfItem('core-config');
        $this->addAcfItem('post-hat');
        $this->addAcfItem('post-notes');
        $this->addAcfItem('post-tags');
        $this->addAcfItem('post-credits');
        $this->addAcfItem('taxonomy-category');
        $this->addAcfItem('taxonomy-credit');
        $this->addAcfItem('attachment-credit');
        $this->registerAcf();
    }
}
