<?php

namespace Morpheus\Modules\PushNotification;

use DateTime;
use DateTimeZone;

class Helpers
{
    /**
     * Retorna um array com os dados do usuário que executou
     * @return array
     */
    public static function getAuthor(): array
    {
        $user = wp_get_current_user();
        return [
            'id'            => $user->ID,
            'name'          => $user->display_name
        ];
    }

    /**
     * Seta o BlogId se tiver o blogPath
     * @param array $data
     * @return array
     */
    public static function setBlogId(array $data): array
    {
        if (!is_multisite()) return $data;
        if (empty($data['blogPath'])) return $data;
        $siteDomain = preg_replace('/^https?:\/\//([^\/])\/', '$1', get_site_url());
        $data['blogId'] = get_blog_id_from_url($siteDomain, $data['blogPath']);
        if ($data['blogId']) switch_to_blog($data['blogId']);
        return $data;
    }

    /**
     * Seta a imagem de thumbnail do post no $data
     * @param array $data
     * @return array
     */
    public static function setImage(array $data): array
    {
        if (empty($data['thumbnailId'])) return $data;
        $data['image'] = wp_get_attachment_image_url($data['thumbnailId'], 'cardnews_vertical');
        return $data;
    }

    /**
     * Seta o atributo de dados personalizados 'data'
     * @param array $data
     * @return array
     */
    public static function setCustomData(array $data): array
    {
        // $user = wp_get_current_user();
        $data['data'] = [
            'postId'        => $data['postId'],
            'thumbnailId'   => $data['thumbnailId'],
            'author'        => Helpers::getAuthor()
        ];
        return $data;
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
        return DateTime::createFromFormat(
            $format,
            $date,
            new DateTimeZone($timezone)
        );
    }
}
