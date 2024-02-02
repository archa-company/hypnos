<?php

namespace Morpheus\Modules\Core\Events\Payload;

use Morpheus\Contracts\Exportable;
use Morpheus\Shared\Dev;
use Morpheus\Shared\Helper;
use Morpheus\Shared\Traits\UseConfig;
use stdClass;
use WP_Post;
use WP_Term;

class MenuPayload implements Exportable
{
    use UseConfig;

    private array $menus = [];

    public function export()
    {
        return $this->parse();
    }

    private function parse(): array
    {

        return [
            'type'              => 'menu',
            'config'            => $this->getConfigs(),
            'payload'           => [
                'site'              => $this->getConfig('domain_id'),
                'menus'             => $this->getMenus(),
            ],
        ];
    }

    private function getMenus()
    {
        $navMenus = get_terms('nav_menu', ['hide_empty' => true]);
        foreach ($navMenus as $menu) $this->getMenu($menu);
        $this->sanitize();
        return $this->menus;
    }

    private function sanitize()
    {
        foreach ($this->menus as &$menu) {
            $menu = $this->sanitizeItems($menu);
        }
    }

    private function sanitizeItems($menu)
    {
        if (empty($menu->items)) return $menu;
        $menu->items = array_values($menu->items);
        foreach ($menu->items as &$submenu) $this->sanitizeItems($submenu);
        return $menu;
    }

    private function getMenu(WP_Term $menu)
    {
        $menuItems = wp_get_nav_menu_items($menu->term_id);
        foreach ($menuItems as $menuItem) $this->getItem($menu->slug, $menuItem);
        $this->menus[$menu->slug] = (object) [
            'name' => $menu->name,
            'items' => $this->menus[$menu->slug]
        ];
    }

    private function getItem(string $menuSlug, WP_Post $menuItem)
    {
        $data = (object) [
            'id' => $menuItem->ID,
            'name' => $menuItem->title,
            'url' => Helper::removeDomain($menuItem->url),
            'target' => $menuItem->target ?: null,
            'title' => $menuItem->attr_title ?: null,
            'description' => $menuItem->description ?: null,
            'classes' => implode(' ', $menuItem->classes) ?: null,
            'parent' => (int) $menuItem->menu_item_parent ?: null,
            // 'type' =>  [
            //     'name' => $menuItem->type ?: null,
            //     'label' => $menuItem->type_label ?: null,
            //     'object' => $menuItem->object ?: null,
            // ],
            'items' => [],
        ];

        $this->addItem($menuSlug, $data);
    }

    private function addItem(string $menuSlug, stdClass $item): void
    {
        if ($item->parent) return $this->addSubItem($menuSlug, $item);
        $this->menus[$menuSlug][$item->id] = $item;
    }

    private function addSubItem(string $menuSlug, stdClass $item): void
    {
        $this->menus[$menuSlug][$item->parent]->items[$item->id] = $item;
    }
}
