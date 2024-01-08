<?php

namespace Morpheus\Modules\Core\Classes\Tracker;

use Morpheus\Shared\Traits\FactoryMethod;
use Morpheus\Shared\Traits\UseConfig;
use WP_Post;

class Tracker
{

    use UseConfig, FactoryMethod;

    public function getPostId()
    {
        $object = get_queried_object();
        return ($object instanceof WP_Post) ? $object->ID : null;
    }

    public function displayAmp()
    {
        $postId = $this->getPostId();
        $siteName = $this->getConfig('analytics_gp_tracker_id');

        $params = [
            "extraUrlParams" => [
                "site" => $siteName,
                "section" => "web-stories",

                "contentId" => TrackerUuid::get($postId),
                "title" => get_the_title(),
                "article" => get_post_field('post_name', $postId),
                "statsPushedAt" => get_the_date('c', $postId), //"2020-02-02T20:50:39.000Z",
                "thumbUrl" => (has_post_thumbnail($postId)) ? get_the_post_thumbnail_url($postId, 'post-cover') : "",

                "statsCategories" => "", //"protagonismo,ong,saude,valor-da-familia",
                "verbs" => "",

                "_" => "\${random}",
                "referrer" => "\${documentReferrer}",
                "resolution" => "\${screenWidth}x\${screenHeight}",
                "statsStartDecayAfterDays" => null,
                "statsWeight" => "1",
                "timezone" => "\${timezoneCode}",
                "url" => "\${ampdocUrl}",
                "userAgent" => "\${userAgent}",
            ],
            "requests" => [
                "hit" => "https://amp-analytics.gazetadopovo.com.br/hit/",
                "paywall" => "https://amp-analytics.gazetadopovo.com.br/paywall/",
                "read" => "https://amp-analytics.gazetadopovo.com.br/read/"
            ],
            "triggers" => [
                "trackHit" => [
                    "on" => "ini-load",
                    "request" => "hit"
                ],
                "trackPaywall" => [
                    "on" => "timer",
                    "request" => "paywall",
                    "timerSpec" => [
                        "immediate" => false,
                        "interval" => 2,
                        "maxTimerLength" => 1.9,
                        "startSpec" => [
                            "on" => "subscriptions-access-denied"
                        ]
                    ]
                ],
                "trackRead" => [
                    "on" => "timer",
                    "request" => "read",
                    "timerSpec" => [
                        "immediate" => false,
                        "interval" => 10,
                        "maxTimerLength" => 9.9,
                        "startSpec" => [
                            "on" => "subscriptions-access-granted"
                        ]
                    ]
                ]
            ]
        ];

        $params = json_encode($params);
        echo "<amp-analytics id=\"gp-tracker\"><script type=\"application/json\">{$params}</script></amp-analytics>";
    }
}
