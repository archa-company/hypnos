<?php

namespace Morpheus\Modules\WebStories\Classes;

use Morpheus\Modules\Core\Classes\AmpAnalytics;
use Morpheus\Modules\Core\Classes\Tracker\Tracker;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\UseConfig;

class Analytics
{
    use HasHooks, UseConfig;

    public function __construct()
    {
        $this->addAction('web_stories_print_analytics',   [$this, 'analyticsAdditional'], 11);
        $this->registerHooks();
    }

    public function analyticsAdditional()
    {
        $analytcs = AmpAnalytics::factory();

        $this->ga4();
        $this->ga4Business();
        $this->ga3();
        Tracker::factory()->displayAmp();
        $analytcs->netdeal(["Stories"]);
        $analytcs->cxense();
    }

    public function ga4()
    {
        if (!empty($this->getOption("web_stories_ga_tracking_id"))) return;
        $gaId = $this->getConfig('analytics_ga4_id');
        if (empty($gaId)) return;
        $this->gaAnalytics($gaId);
    }

    public function ga4Business()
    {
        $gaId = $this->getConfig('analytics_ga4_business_id');
        if (empty($gaId)) return;
        $this->gaAnalytics($gaId);
    }

    public function ga3()
    {
        $gaId = $this->getConfig('analytics_ga3_id');
        if (empty($gaId)) return;
        $this->gaAnalytics($gaId);
    }

    public function gaAnalytics(string $gtagId)
    {
        echo "<amp-story-auto-analytics gtag-id=\"$gtagId\"></amp-story-auto-analytics>";
    }
}
