<?php

namespace Morpheus\Modules\Core\Hooks;

use Morpheus\Contracts\Filterable;
use Morpheus\Shared\Traits\UseConfig;

class AdminHomeLink implements Filterable
{

    use UseConfig;

    /**
     * Altera o link de permalink e preview Link para o Post Type
     *
     * @param string $link
     * @param WP_Post $post
     * @return string
     */
    public function __invoke(...$params): string
    {
        /**
         * @param string $link
         * @param WP_Post $post
         * @return string
         */
        [$link, $post] = $params;

        if ($post->post_type !== 'home') return $link;
        if ($post->post_status !== 'publish') return $link;

        $baseLink = $this->getConfig('domain_oficial');
        $hasHttps = $this->getConfig('domain_https') ?? false;
        $protocol = $hasHttps ? 'https' : 'http';
        $link = preg_replace('/^https?:\/\/[^\/]+(.*)$/', '$1', $link);
        $link = preg_replace('/^\/home(.*)$/', '$1', $link);
        $link = $link === '/home/' ? '/' : $link;

        return "{$protocol}://{$baseLink}{$link}";
    }
}
