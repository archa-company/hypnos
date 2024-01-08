<?php

namespace Morpheus\Shared;

use Exception;
use WP_Post;
use WP_Term;

class Helper
{

    public static function removeDomain(string $link)
    {
        return preg_replace('/^https?:\/\/[^\/]+(.*)$/', '$1', $link);
    }

    public static function removeProtocol(string $link)
    {
        return preg_replace('/^https?:\/\/(.*)$/', '$1', $link);
    }

    public static function getRelativePermalink(WP_Post | int $post)
    {
        return self::removeDomain(get_permalink($post));
    }

    public static function getRelativeTermLink(WP_Term | int | string $term)
    {
        return self::removeDomain(get_term_link($term));
    }

    public static function toCamelCase(string $input, string $separator = '_'): string
    {
        return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
    }

    public static function getContentHtml(string $permalink)
    {
        $response = wp_remote_get($permalink, [
            'timeout'       => 120,
            'httpversion'   => '1.1',
            'headers'       => [
                'Accept'        => 'text/html',
            ],
        ]);
        if (is_wp_error($response) || !is_array($response) || $response['response']['code'] !== 200) return null;
        return $response['body'];
    }

    public static function getContentHtmlById(int $id)
    {
        $permalink = get_permalink($id);
        if (!$permalink) return null;
        return self::getContentHtml($permalink);
    }

    public static function getContentHtmlToJson(string $permalink)
    {
        $html = self::getContentHtml($permalink);
        return json_encode($html);
    }

    public static function getSite(string $domain)
    {
        return preg_replace('/[^\.]*?\.?([^\.]+)\.com\.br$/', '$1', $domain);
    }

    /**
     * Verifica se um usuário tem uma determinada Role/Função
     *
     * @param int $userId
     * @param string $roleName
     * @return bool
     */
    public static function hasUserRole(int $userId, string $roleName): bool
    {
        $user = get_userdata($userId);
        return in_array($roleName, $user->roles);
    }

    public static function isWpJsonRequest()
    {
        if (isset($_SERVER['REQUEST_URI']) && preg_match('/wp-json/', $_SERVER['REQUEST_URI'], $match, PREG_UNMATCHED_AS_NULL)) return true;
        return false;
    }

    public static function preventTwiceHook(string $hookName, int $postId, int $timeoutSeconds = 10, bool $onWpJson = false)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return true;
        if (wp_is_post_revision($postId)) return true;
        if (wp_is_post_autosave($postId)) return true;
        if ($onWpJson && self::isWpJsonRequest()) return true;
        if (self::isPreventTwice($hookName, $postId, $timeoutSeconds)) return true;
        return false;
    }

    public static function isPreventTwice(string $namespace, int $postId, $timeoutSeconds = 10)
    {
        $namespace = Helper::getClassShortName($namespace);
        $key = "morpheus_prevent_twice_{$postId}_{$namespace}";
        if (get_transient($key)) return true;
        set_transient($key, '1', $timeoutSeconds);
        return false;
    }

    public static function getClassShortName(string $class): string
    {
        return basename(str_replace('\\', '/', $class));
    }

    public static function getNumberPages(int $count = 1, int $perPage = 10)
    {
        return ceil($count / $perPage);
    }

    /**
     * Calcula o tempo de leitura de uma texto
     * @param string $text
     * @return int
     */
    public static function readTime($text)
    {
        $words = str_word_count(strip_tags($text));
        if (empty($words)) return false;
        return ceil($words / 200);
    }

    /**
     * Retorna a Key para salvar no S3
     *
     * @param string $link
     * @param string $prefix
     * @return string
     */
    public static function linkToKey($link, $prefix = 'json/', $sufix = '.json')
    {
        // $key        = str_replace(WP_HOME, '', $link); // remove o domínio
        $key        = preg_replace('/^(?:https?:\/\/)?(?:[^@\n]+@)?(?:www\.)?([^:\/\n?]+)\//', '', $link); // remove o domínio
        $key        = preg_replace('/\/$/', '', $key); // remove a barra do final
        return "{$prefix}{$key}{$sufix}";
    }

    public static function isSitePath(string $path, $blogId = null): bool
    {
        $blogId = $blogId ?? get_current_blog_id();
        $blogDetails = get_blog_details($blogId);
        return ($blogDetails->path === "/{$path}/");
    }

    /**
     * Verifica se tem algum tipo de mídia como iframe, audio, vídeo ou imagem no conteúdo
     * @param string $content
     * @return boolean
     */
    public static function hasMedia($content)
    {
        $hasMedia = preg_match('/(<(iframe|video|audio|picture|figure|img|amp-img|amp-video|amp-audio|amp-youtube)[^>]*>)/', $content);
        return ($hasMedia !== FALSE) ? (bool) $hasMedia : false;
    }

    public static function getTags($content, $tag)
    {
        preg_match_all("/<{$tag}[^>]*>(.*)<\/{$tag}>/", $content, $matches);
        return current($matches);
    }

    public static function getPrimaryCategory($postId = null)
    {
        if (!class_exists('\WPSEO_Primary_Term')) return false;
        $postId = (empty($postId)) ? get_the_ID() : $postId;

        $cat = new \WPSEO_Primary_Term('category', $postId);
        $termId = $cat->get_primary_term();
        $category = get_category($termId);
        return $category;
    }

    /**
     * Retorna o ID de um termo
     *
     * @param string $slug
     * @param string $taxonomy O valor padrão é 'category'
     * @return int
     */
    public static function getTermIdBySlug(string $slug, string $taxonomy = 'category'): int
    {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare("SELECT t.term_id FROM {$wpdb->terms} t INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE t.slug = '%s' AND tt.taxonomy = '%s'", $slug, $taxonomy));
    }

    /**
     * Faz o redirect efetivamente
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    public static function redirectTo(string $url, int $status = 301): void
    {
        header("HTTP/1.1 {$status} Moved Permanently");
        header("Location: {$url}");
        header("Connection: close");
        die;
    }

    public static function removeFirstLastSlashes(string $content)
    {
        return preg_replace("/^\/+|\/+$/", "", $content);
    }

    /**
     * Valida se o Path atual é corresponde ao desejado
     *
     * @param string $searchPath
     * @param string $serverIndex   Campo do $_SERVER onde será verificado
     * @return bool
     */
    public static function isCurrentPath(string $searchPath, string $serverIndex = 'REQUEST_URI'): bool
    {
        $path = self::removeFirstLastSlashes($searchPath);
        $currentPath = self::removeFirstLastSlashes($_SERVER[$serverIndex]);
        return ($path === $currentPath);
    }

    /**
     * Valida se o site/blog atual começa com /blogs/
     */
    public static function isBlogs(int $blogId = null): bool
    {
        $blogId = $blogId ?? get_current_blog_id();
        $blogPath = self::getBlogDetailsPath($blogId);
        return self::hasBlogsInPath($blogPath);
    }

    public static function hasBlogsInPath(string $blogPath): bool
    {
        $path = self::removeFirstLastSlashes($blogPath);
        $pathArray = explode('/', $path);
        if (count($pathArray) < 2) return false;
        if (current($pathArray) !== 'blogs') return false;
        return true;
    }

    public static function getBlogDetailsPath(int $blogId)
    {
        $blogDetails = get_blog_details($blogId);
        return $blogDetails->path;
    }

    public static function getBlogDetailsSlug(int $blogId)
    {
        $blogPath = self::getBlogDetailsPath($blogId);
        return preg_replace('/^\/?(blogs\/)?(.*)\/$/', '$2', $blogPath);
    }

    /**
     * Retorna a slug do blog
     *
     * @param string $uri
     */
    public static function getBlogSlug(string $uri): ?string
    {
        $uri = self::removeFirstLastSlashes($uri);
        $listFolders = explode('/', $uri);
        return end($listFolders);
    }

    /**
     * Retorna a categoria Pai/Ancestral de primeiro nível
     *
     * @param WP_Term $term
     * @return WP_Term
     */
    public static function getTopLevelTerm(WP_Term $term): WP_Term
    {
        if (!$term->parent) return $term;
        $term = get_term($term->parent, $term->taxonomy);
        return (!$term->parent)
            ? $term
            : self::getTopLevelTerm($term);
    }

    /**
     * Cria uma data com base em um formato
     *
     * @param mixed $date
     * @param string $format Formato padrão 'd/m/Y'
     * @param string $timezone Padrão 'America/Sao_Paulo'
     * @return DateTime
     */
    public static function getDateTimeObject($date, string $format = 'd/m/Y', string $timezone = 'America/Sao_Paulo')
    {
        return \DateTime::createFromFormat(
            $format,
            $date,
            new \DateTimeZone($timezone)
        );
    }

    /**
     * Valida se é um JSON
     *
     * @param string $content
     * @return bool
     */
    public static function isJson(string $content): bool
    {
        return (json_decode($content) == NULL) ? false : true;
    }

    /**
     * Converte uma Imagem em data:base64
     *
     * @param string $imageUrl
     * @return string
     */
    public static function imageToBase64(string $imageUrl): string
    {
        $type = pathinfo($imageUrl, PATHINFO_EXTENSION);
        $data = file_get_contents($imageUrl);
        return "data:image/{$type};base64," . base64_encode($data);
    }

    /**
     * Retorna a URL do Thumbnail de um Post em um Blog
     *
     * @param int $blogId
     * @param int $postId
     * @param string $imageSize
     * @return string|null
     */
    public static function getBlogPostThumbnailUrl(int $blogId, int $postId, string $imageSize = 'cardnews_vertical'): ?string
    {
        switch_to_blog($blogId);
        $image = get_the_post_thumbnail_url($postId, $imageSize) ?? null;
        restore_current_blog();
        return $image;
    }

    public static function removeAccents(string $string): string
    {
        $string = trim($string);
        $before = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ';
        $after  = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBSaaaaaaaceeeeiiiidnoooooouuuyyby';
        return strtr($string, $before, $after);
    }
}
