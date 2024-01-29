<?php
namespace Morpheus\Modules\LandingPages;

use Morpheus\Contracts\ModuleInterface;
use Morpheus\Shared\Dev;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\Singleton;
use Morpheus\Shared\Traits\UseConfig;
class Module implements ModuleInterface
{
    use Singleton, HasHooks, UseConfig;
    
    public function init(): void
    {
      add_action('init',function(){
       register_post_type('special',[
        'supports'=>['title','editor','author','thumbnail','excerpt','custom-fields','comments','revisions','post-formats'],
        'labels'=>[
         'name'=>__('Landing Pages'),
         'singular_name'=>__('Landing Page'),
         'menu_name'=>__('Criador de Landing Pages'),
         'all_items'=>__('Todas as Páginas'),
         'add_new'=>__('Adicionar Novo'),
         'add_new_item'=>__('Adicionar Landing Page'),
         'new_item'=>__('Nova Landing Page'),
         'edit_item'=>__('Editar Landing Page'),
         'view_item'=>__('Ver Landing Page'),
         'view_items'=>__('Ver Landing Pages'),
         'search_items'=>__('Buscar Landing Page'),
         'not_found'=>__('Landing Page Não Encontrada'),
         'not_found_in_trash'=>__('Landing Page Não Encontrada na Lixeira'),
         'featured_image'=>__('Imagem de Destaque'),
         'set_featured_image'=>__('Definir Imagem de Destaque da LP'),
         'remove_featured_image'=>__('Remover Imagem de Destaque da LP'),
         'use_featured_image'=>__('Usar Imagem de Destaque da LP'),
         'archives'=>__('Arquivo de Landing Page'),
         'insert_into_item'=>__('Inserir nesta Landing Page'),
         'uploaded_to_this_item'=>__('Enviado para esta Landing Page'),
         'filter_items_list'=>__('Filtrar Lista de Landing Pages'),
         'items_list_navigation'=>__('Navegação na Lista de Landing Pages'),
         'items_list'=>__('Lista de Landing Pages'),
         'attributes'=>__('Atributos da Landing Page'),
         'item_published'=>__('Landing Page Publicada'),
         'item_published_privately'=>__('Landing Page Publicada de Forma Privada'),
         'item_reverted_to_draft'=>__('Landing Page Revertida para Rascunho'),
         'item_scheduled'=>__('Landing Page Agendada'),
         'item_updated'=>__('Landing Page Atualizada'),
         'parent_item_colon'=>__('Ascendente'),
         'enter_title_here'=>__('Adicionar Título à Landing Page'),
         'name_admin_bar'=>__('Landing Page')
        ],
        'taxonomies'=>['category','post_tag','credit'],
        'menu_icon'=>'dashicons-media-document',
        'public'=>true,
        'has_archive'=>false,
        'query_var'=>false,
        'has_archive'=>true,
        'hierarchical'=>false,
        'show_in_nav_menus'=>true,
        'show_in_menu'=>true,
        'show_in_admin_bar'=>true,
        'show_in_rest'=>true,
        'exclude_from_search'=>true,
        'show_ui'=>true,
        'Menu_position'=>5,
        'rewrite'=>['slug'=>'news'],
        'description'=>'Criador de Landing Pages']
       );
      });
    }
}
