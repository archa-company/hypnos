<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\UseConfig;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class ElasticSearch
{
    use HasHooks, UseConfig;

    public const MAPPER_MODEL = ModelMorpheus::class;

    public function __construct()
    {
        $this->addAction('rest_api_init', [$this, 'registerRoutes']);
        $this->registerHooks();
    }

    private function getEndpointUrl()
    {
        $domain = $this->getConfig('hermes_elasticsearch_endpoint');
        $withPrefix = $this->getConfig('hermes_elasticsearch_prefix');
        $siteId = $this->getConfig('domain_id');
        $index = $withPrefix ? sprintf("%s_posts", $siteId) : 'posts';
        return sprintf("%s/%s/_search", $domain, $index);
    }

    private function getAuthentication(): array
    {
        $username = $this->getConfig('hermes_elasticsearch_user');
        if (!$username) return [];
        $password = $this->getConfig('hermes_elasticsearch_pass');
        $login = base64_encode(sprintf("%s:%s", $username, $password));
        return [
            'Authorization' => sprintf("Basic %s", $login),
        ];
    }

    public function registerRoutes()
    {
        register_rest_route('morpheus/v1', '/elasticsearch', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'response'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function getQuery(array $query): array
    {
        $siteId = $this->getConfig('domain_id');
        if (!$siteId) return $query;
        $query['query']['bool']['filter'][] = ['term' => ['site' => $siteId]];
        return $query;
    }

    public function response(WP_REST_Request $request)
    {
        if (!$request->is_json_content_type()) return new WP_Error('rest_content_type_error', 'Content Type inválido', ['status' => 415]);

        $payload = $this->getQuery($request->get_json_params());
        $headers = [...$this->getAuthentication(), 'Content-Type' => 'application/json'];
        // dd($headers);
        $esResponse = wp_remote_post($this->getEndpointUrl(), ['headers' => $headers, 'body' => json_encode($payload)]);
        // dd($esResponse);
        if (is_wp_error($esResponse)) return new WP_Error('elasticsearch_error', 'Erro do ElasticSearch', ['status' => 500, 'error' => $esResponse->get_error_message()]);

        $body = json_decode(wp_remote_retrieve_body($esResponse));
        $result = Mapper::factory($body->hits->hits, self::MAPPER_MODEL)->process()->getResult();

        $response = new WP_REST_Response($result);
        $response->set_status(200);
        $response->header('Content-Type', 'application/json');
        return $response;
    }
}
