<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

use Morpheus\Shared\Traits\HasHooks;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class ElasticSearch
{
    use HasHooks;

    public const ENDPOINT_URL = 'https://vpc-gp-prd-es-5lbr4vnlwecm4f6uraclk7uqty.us-east-1.es.amazonaws.com/caixa/_search';

    public function __construct()
    {
        $this->addAction('rest_api_init', [$this, 'registerRoutes']);
        $this->registerHooks();
    }

    public function registerRoutes()
    {
        register_rest_route('morpheus/v1', '/elasticsearch', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'response'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function response(WP_REST_Request $request)
    {
        if (!$request->is_json_content_type()) return new WP_Error('rest_content_type_error', 'Content Type inválido', ['status' => 415]);

        $payload = $request->get_json_params();
        $esResponse = wp_remote_post(self::ENDPOINT_URL, ['headers' => ['Content-Type' => 'application/json'], 'body' => json_encode($payload)]);
        if (is_wp_error($esResponse)) return new WP_Error('elasticsearch_error', 'Erro do ElasticSearch', ['status' => 500, 'error' => $esResponse->get_error_message()]);

        $body = json_decode(wp_remote_retrieve_body($esResponse));
        $result = Mapper::factory($body->hits->hits, ModelGazeta::class)->process()->getResult();

        $response = new WP_REST_Response($result);
        $response->set_status(200);
        $response->header('Content-Type', 'application/json');
        return $response;
    }
}
