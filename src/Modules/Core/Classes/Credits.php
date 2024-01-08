<?php

namespace Morpheus\Modules\Core\Classes;

use Morpheus\Shared\Traits\HasHooks;

class Credits
{

    use HasHooks;

    public function __construct()
    {
        $this->addAction('init',                          [$this, 'registerPostType']);
        $this->addFilter('register_taxonomy_args',        [$this, 'hideNativeBox'], 10, 3);
        $this->addFilter('acf/fields/taxonomy/query',     [$this, 'acfFieldTaxonomyQuery'], 10, 3);
        // $this->addFilter('acf/fields/taxonomy/result',    [$this, 'acfFieldTaxonomyResult'], 10, 4);
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
        if ($taxonomy !== 'credit') return $args;
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
        if ($field['name'] !== 'credits') return $args;
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
        if ($field['name'] !== 'credits') return $text;
        $text = "{$term->name}";
        return $text;
    }

    public function registerPostType(): void
    {
        register_taxonomy('credit', 'post', [
            'labels'                => [
                'name'                  => 'Créditos',
                'singular_name'         => 'Crédito',
                'add_new_item'          => 'Adicionar Novo Crédito',
                'add_or_remove_items'   => 'Adicionar ou Remover Crédito',
                'edit_item'             => 'Editar Crédito',
                'new_item'              => 'Novo Crédito',
                'view_item'             => 'Visualizar Crédito',
                'search_items'          => 'Buscar Créditos',
                'all_items'             => 'Todos as Crédito',
                'not_found'             => 'Nenhum Crédito encontrado',
            ],
            'description'           => 'Creditos das matérias. Editor, Redator, Autor, Jornalista, Agência, etc.',
            'public'                => true,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_in_rest'          => true,
            'show_in_menu'          => true,
            'show_in_quick_edit'    => false,
            'show_in_nav_menus'     => false,
            'show_admin_column'     => true,
            'meta_box_cb'           => false,
            'rewrite'               => [
                'slug'                  => 'autor'
            ],
            'default_term'          => [
                'name'                  => 'Redação',
                'slug'                  => 'redacao',
            ]
        ]);
    }
}
