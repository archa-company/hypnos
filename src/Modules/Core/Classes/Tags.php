<?php

namespace Morpheus\Modules\Core\Classes;

use Morpheus\Shared\Traits\HasHooks;

class Tags
{

    use HasHooks;

    public function __construct()
    {
        $this->addFilter('register_taxonomy_args',        [$this, 'hideNativeBox'], 10, 3);
        $this->addFilter('acf/fields/taxonomy/query',     [$this, 'acfFieldTaxonomyQuery'], 10, 3);
        $this->addFilter('acf/fields/taxonomy/result',    [$this, 'acfFieldTaxonomyResult'], 10, 4);
        $this->registerHooks();
    }

    /**
     * Oculta as Tags Nativas de Post
     * para usar no lugar um ACF com permissionamento especial
     *
     * @uses hook register_taxonomy_args
     * @param array $args
     * @param string $taxonomy
     * @param string $postType
     * @return array
     */
    public function hideNativeBox($args, $taxonomy, $postType): array
    {
        if ($taxonomy !== 'post_tag') return $args;
        $args = array_merge($args, [
            'show_in_nav_menus'     => false,
            'show_in_quick_edit'    => false,
            'show_in_rest'          => false,
            'meta_box_cb'           => false,
        ]);
        return $args;
    }

    /**
     * Filtra o campo de tags para ordenar pela quantidade de posts
     * @param array $args
     * @param array $field
     * @param int $postId
     * @return array
     */
    public function acfFieldTaxonomyQuery($args, $field, $postId): array
    {
        if ($field['name'] !== 'tags') return $args;
        $args['orderby'] = 'count';
        $args['order'] = 'DESC';
        return $args;
    }

    /**
     * Personaliza o retorno do campo de tags mostrando a quantidade de posts
     * @param string $text
     * @param WP_Term $term
     * @param array $field
     * @param int $postId
     * @return string
     */
    public function acfFieldTaxonomyResult($text, $term, $field, $postId): string
    {
        if ($field['name'] !== 'tags') return $text;
        $text = "{$term->name} ({$term->count} posts)";
        return $text;
    }
}
