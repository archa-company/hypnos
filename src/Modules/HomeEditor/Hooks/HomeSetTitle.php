<?php

namespace Morpheus\Modules\HomeEditor\Hooks;

use Morpheus\Contracts\Actionable;

class HomeSetTitle implements Actionable
{
    /**
     * Seta um título automaticamente à home
     */
    public function __invoke(...$params): array
    {
        /**
         * @var array @data Dados que serão salvos
         * @var array @pastarr Dados para conferencia
         */
        [$data, $postarr] = $params;
        if (isset($data['post_type']) && $data['post_type'] != 'home') return $data;
        if (isset($data['post_status']) && in_array($data['post_status'], ['auto-draft', 'trash'])) return $data;

        $date   = date_i18n('d-m-Y H:i');
        $author = get_user_by('ID', $data['post_author']);

        $data['post_title'] = "Home - {$date} - ({$author->display_name})";

        $postId = $postarr['ID'] ?: 0;
        $data['post_name']  = wp_unique_post_slug(sanitize_title($data['post_title']), $postId, $data['post_status'], 'home', 0);

        return $data;
    }
}
