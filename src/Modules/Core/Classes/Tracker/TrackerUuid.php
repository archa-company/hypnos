<?php

namespace Morpheus\Modules\Core\Classes\Tracker;

use WP_Post;
use WP_Term;

class TrackerUuid
{
    const META_KEY = 'tracker_uuid';

    public static function generate($postId, $type = 'post')
    {
        $uuidV4 = UUID::v4();
        return UUID::v5($uuidV4, "TRB_Tracker_{$type}_{$postId}");
    }

    public static function set(int $postId): string
    {
        $uuid = self::generate($postId, 'post');
        add_post_meta($postId, self::META_KEY, $uuid, true);
        return $uuid;
    }

    public static function get(int $postId): string
    {
        $uuid = get_post_meta($postId, self::META_KEY, true);
        if (empty($uuid)) $uuid = self::set($postId, 'post');
        return $uuid;
    }

    public static function setFromTerm(int $termId): string
    {
        $uuid = self::generate($termId, 'term');
        add_term_meta($termId, self::META_KEY, $uuid, true);
        return $uuid;
    }

    public static function getFromTerm(int $termId): string
    {
        $uuid = get_term_meta($termId, self::META_KEY, true);
        if (empty($uuid)) $uuid = self::set($termId, 'term');
        return $uuid;
    }

    public static function getFromObject(WP_Post | WP_Term $object): string
    {
        $objectId = match (get_class($object)) {
            WP_Post::class => $object->ID,
            WP_Term::class => $object->term_id,
        };
        $function = match (get_class($object)) {
            WP_Post::class => 'get_post_meta',
            WP_Term::class => 'get_term_meta',
        };
        $uuid = $function($objectId, self::META_KEY, true);
        if (empty($uuid)) $uuid = self::setFromObject($object);
        return $uuid;
    }

    public static function setFromObject(WP_Post | WP_Term $object): string
    {
        $objectId = match (get_class($object)) {
            WP_Post::class => $object->ID,
            WP_Term::class => $object->term_id,
        };
        $type = match (get_class($object)) {
            WP_Post::class => 'post',
            WP_Term::class => 'term',
        };
        $function = match (get_class($object)) {
            WP_Post::class => 'add_post_meta',
            WP_Term::class => 'add_term_meta',
        };
        $uuid = self::generate($objectId, $type);
        $function($objectId, self::META_KEY, $uuid, true);
        return $uuid;
    }
}
