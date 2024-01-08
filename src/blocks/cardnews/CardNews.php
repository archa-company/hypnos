<?php

namespace Morpheus\blocks\cardnews;

use stdClass;
use WP_Post;

class CardNews
{
    public ?WP_Post $post = null;
    public ?int $postId = null;
    public string $imageSize = "large";

    public function __construct(?int $postId = null)
    {
        $post = get_field("postObject");

        if (!$this->isPost($post) && $postId) {
            $post = WP_Post::get_instance($postId);
        }

        if (!$this->isPost($post)) return;

        $this->post = $post;
        $this->postId = $this->post->ID;
    }

    public static function factory()
    {
        return new CardNews();
    }

    public static function parseBlock()
    {
        return CardNews::factory()->toObject();
    }

    public function getFields(): array
    {
        return [
            'postId'            => $this->postId,
            'layout'            => $this->getField("layout"),
            'format'            => $this->getField("format"),
            'size'              => $this->getField("size", 'medium'),
            'hat'               => $this->getHat(),
            'title'             => $this->getField("title", $this->postId ? get_the_title($this->postId) : ''),
            'link'              => $this->getField("link", $this->postId ? get_permalink($this->postId) : ''),
            'image'             => $this->getImage(),
            'inverted'          => $this->getField("inverted", false),
            'boxed'             => $this->getField("boxed", false),
            'sponsored'         => $this->getSponsored(),
            'sponsorLabel'      => $this->getSponsorLabel(),
            'sponsorName'       => $this->getField("sponsor"),
            'relateds'          => $this->getField("relateds"),
        ];
    }

    private function getField(string $key, $default = null)
    {
        return get_field($key) ?: $default;
    }

    private function getHat()
    {
        if ($this->isSponsored()) return $this->getSponsorLabel();
        $default = (!empty($this->postId)) ? get_post_meta($this->postId, 'sobretitulo', true) : null;
        return $this->getField("hat", $default);
    }

    private function getImage(): string|null
    {
        if (!get_field("hasImage")) return null;
        $default = (!empty($this->postId)) ? get_the_post_thumbnail_url($this->postId, $this->imageSize) : null;
        return $this->getField("image", $default);
    }

    private function isSponsored(): bool
    {
        return $this->getField("sponsored", 'none') !== 'none';
    }

    private function getSponsored()
    {
        if (!$this->isSponsored()) return false;
        return $this->getField("sponsored");
    }

    private function getSponsorLabel()
    {
        $labels = [
            "sponsored"     => "Especial Patrocinado",
            "advertising"   => "Especial Publicitário",
            "none"          => "",
        ];
        return $labels[$this->getField("sponsored", "none")];
    }

    private function isPost($post)
    {
        return $post instanceof WP_Post;
    }

    public function toArray(): array
    {
        return $this->getFields();
    }

    public function toObject(): stdClass
    {
        return (object) $this->getFields();
    }
}
