<?php

namespace Morpheus\Shared\Classes;

use stdClass;

class ContentBlocks
{

    public array $removeContent = [
        'core/columns',
        'core/column',
        'core/cover',
        'core/image',
        'core/list',
    ];

    public function parseBlock(object $block, array $blockOriginal): stdClass
    {
        /** SOMENTE PARA DEBUG */
        // $block->original = $block->content;

        if ($block->name === 'core/paragraph') {
            $block->content = preg_replace('/^<p>(.*)<\/p>$/', "$1", $block->content);
        }

        if ($block->name === 'core/list-item') {
            $block->content = preg_replace('/^<li>(.*)<\/li>$/', "$1", $block->content);
        }

        if ($block->name === 'core/heading') {
            // preg_match('/^<h(?<size>[1-6]{1})[^>]*>(?<fullContent>(?:<a(?: href="(?<href>[^"]+)")(?: target="(?<target>[^"]+)")?[^>]+>(?<linkContent>.*)<\/a>.*|.*))<\/h[1-6]{1}>$/', $block->content, $matches);
            preg_match('/^<h(?<size>[1-6]{1})[^>]*>(?<content>(?:<a(?: href="(?<href>[^"]+)")(?: target="(?<target>[^"]+)")?[^>]+>)?.*)<\/h[1-6]{1}>$/', $block->content, $matches);
            $block->props = array_merge($block->props, array_filter([
                'size' => $matches['size'],
                'href' => $matches['href'],
                'target' => $matches['target'],
            ]));
            $block->content = strip_tags($block->content);
        }

        if ($block->name === 'core/image') {
            preg_match('/<img(?: src="(?<source>[^"]+)")(?: alt="(?<alt>[^"]+)")?[^>]+>/', $block->content, $matches);
            preg_match('/<figcaption[^>]+>(?<caption>[^<]+)<\/figcaption>/', $block->content, $matches2);
            preg_match('/<a(?: href="(?<href>[^"]+)")(?: target="(?<target>[^"]+)")?[^>]+>/', $block->content, $matches3);
            // dd($matches, $matches2);
            $block->props = array_merge($block->props, array_filter([
                'source' => $matches['source'],
                'alt' => $matches['alt'] ?? null,
                'caption' => $matches2['caption'] ?? null,
                'href' => $matches3['href'] ?? null,
                'target' => $matches3['target'] ?? null,
            ]));
        }

        if ($block->name === 'morpheus/cardnews') {
            $block->props = $this->parseAcfBlock($blockOriginal, ['Morpheus\blocks\cardnews\CardNews', 'parseBlock']);
        }

        $this->blockRemoveContent($block);

        return apply_filters('morpheus_parse_block', $block, $blockOriginal);
    }

    private function blockRemoveContent(object &$block)
    {
        if (!in_array($block->name, apply_filters('morpheus_block_remove_content', $this->removeContent))) return;
        unset($block->content);
    }

    private function parseReusableBlock(object $block)
    {
        if ($block->name !== 'core/block') return $block;
        $post = \WP_Post::get_instance($block->props['ref']);
        return $this->getBlocks($post->post_content);
    }

    public function parseBlocks(array $blocks): array
    {
        $parsedBlocks = [];

        $blocks = array_filter($blocks, function ($block) {
            return $block['blockName'] !== null;
        });

        foreach ($blocks as $blockOriginal) {
            $block = $this->parseBlockBase($blockOriginal);
            $block = $this->parseBlock($block, $blockOriginal);
            $block = $this->parseReusableBlock($block);
            if (is_array($block)) {
                $parsedBlocks = array_merge($parsedBlocks, $block);
            } else {
                array_push($parsedBlocks, $block);
            }
        }

        return array_values($parsedBlocks);
    }

    private function parseBlockBase(array $blockOriginal): stdClass
    {
        $block = (object) $blockOriginal;

        if (isset($block->innerContent)) unset($block->innerContent);

        if ($block->blockName) {
            $block->name = $block->blockName;
            unset($block->blockName);
        }

        $block->props = [];
        if (empty($block->attrs)) unset($block->attrs);
        if (!empty($block->attrs)) {
            $block->props = $block->attrs;
            unset($block->attrs);
        }

        if (empty($block->innerBlocks)) unset($block->innerBlocks);
        if (!empty($block->innerBlocks)) {
            $block->children = $this->parseBlocks($block->innerBlocks);
            unset($block->innerBlocks);
        }

        if (empty($block->innerHTML)) unset($block->innerHTML);
        if (!empty($block->innerHTML)) {
            $block->content = preg_replace('/\n/', '$1', $block->innerHTML);
            unset($block->innerHTML);
        }

        if (preg_match('/^morpheus\//', $block->name)) {
            unset($block->props['id']);
            unset($block->props['name']);
            if (!empty($block->props['data'])) {
                $data = $block->props['data'];
                $data = array_filter($data, function ($item) {
                    return !((bool) preg_match('/^field_/', $item));
                });
                $block->props = array_merge($block->props, $data);
                unset($block->props['data']);
            }
            if (!empty($block->props['mode'])) unset($block->props['mode']);
        }

        return $block;
    }

    public function getBlocks(string $content): array
    {
        $blocks = parse_blocks($content);
        return $this->parseBlocks($blocks);
    }

    public function parseAcfBlock(array $block, callable $callback)
    {
        $block = $block['attrs'];

        $id = acf_get_block_id($block);
        $block['id'] = acf_ensure_block_id_prefix($id);

        // Setup postdata allowing get_field() to work.
        acf_setup_meta($block['data'], $block['id'], true);

        // $result = $callback();
        $result = call_user_func($callback);

        acf_reset_meta($block['id']);

        return $result;
    }

    public static function factory()
    {
        return new ContentBlocks();
    }
}
