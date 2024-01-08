<?php

namespace Morpheus\Modules\Core\Classes;

use Morpheus\Shared\Dev;
use Morpheus\Shared\Helper;
use WP_Post;
use WP_Taxonomy;
use WP_Term;
use WPSEO_Primary_Term;

class EventTaxonomies
{

    public int $postId;
    private WP_Post $post;
    public array $termList = [];
    public array $primaries = [];
    private array $taxonomies = [];

    public function __construct(WP_Post|int $post)
    {
        if ($post instanceof WP_Post) {
            $this->post = $post;
            $this->postId = $post->ID;
        }
        if (is_integer($post)) {
            $this->post = get_post($this->post);
            $this->postId = $this->post->ID;
        }

        $this->getPostTaxonomies();
        foreach ($this->taxonomies as $taxonomy) {
            $this->getPrimaries($taxonomy->name);
            $this->getTerms($taxonomy);
        }
    }

    public function getData()
    {
        if (!empty($this->termList)) return $this->termList;
        return $this->termList;
    }

    private function getTerm(int $termId, string $taxonomy = 'category')
    {
        if (empty($this->termList[$taxonomy])) return $this->sanitizeTerm(get_term($termId, $taxonomy));
        $key = array_search($termId, array_column($this->termList[$taxonomy], 'id'));
        return $this->termList[$taxonomy][$key];
    }

    private function getTerms(WP_Taxonomy $taxonomy)
    {
        $terms = $this->getPostTerms($taxonomy->name);
        if ($taxonomy->hierarchical) $this->getAncestors($terms);
        $this->addTerms($terms);
    }

    private function addTerms(array $terms)
    {
        foreach ($terms as $term) {
            $this->termList[$term->taxonomy][] = $this->sanitizeTerm($term);
        }
    }

    private function sanitizeTerm(WP_Term $term)
    {
        $result = [
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'url' => Helper::removeDomain(get_term_link($term)),
            // 'description' => $term->description,
        ];
        if (!is_taxonomy_hierarchical($term->taxonomy)) return $result;

        $result['isPrimary'] = $this->isPrimary($term->term_id, $term->taxonomy);
        return $result;
    }

    private function getPostTerms(string $taxonomy)
    {
        return wp_get_post_terms($this->postId, $taxonomy, ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all']);
    }

    private function getPostTaxonomies()
    {
        $this->taxonomies = get_object_taxonomies($this->post, 'objects');
    }

    private function getAncestors(array &$terms)
    {
        foreach ($terms as $term) {
            if (!$term->parent) continue;
            $ancestors = get_ancestors($term->term_id, $term->taxonomy, 'taxonomy');
            foreach ($ancestors as $parent) {
                if (array_search($parent, array_column($terms, 'term_id')) === FALSE) {
                    array_unshift($terms, get_term($parent, $term->taxonomy));
                }
            }
        }
    }

    public function getPrimaryId(string $taxonomy = 'category')
    {
        return (new WPSEO_Primary_Term($taxonomy, $this->post->ID))->get_primary_term();
    }

    public function getPrimaryTerm(string $taxonomy = 'category')
    {
        $termId = $this->getPrimaryId($taxonomy);
        if (empty($termId)) return null;
        return $this->getTerm($termId);
    }

    private function getPrimaries(string $taxonomy)
    {
        if (!is_taxonomy_hierarchical($taxonomy)) return;
        $termId = $this->getPrimaryId($taxonomy);
        if (empty($this->getPrimaryId($taxonomy))) return;
        $this->primaries[$taxonomy] = $termId;
    }

    public function isPrimary(int $termId, string $taxonomy): bool
    {
        if (empty($this->primaries[$taxonomy])) return false;
        return $this->primaries[$taxonomy] === $termId;
    }
}
