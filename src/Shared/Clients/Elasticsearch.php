<?php

namespace Morpheus\Shared\Clients;

use Exception;
use Morpheus\Contracts\ClientInterface;


class Elasticsearch implements ClientInterface
{
    public $connection;
    public $endpoint;

    public function __construct()
    {
        try {
            $endpoint = ELASTICSEARCH_ENDPOINT;
            $this->endpoint = "https://{$endpoint}/";
        } catch (Exception $error) {
            throw new Exception('Elasticsearch Endpoint não definido');
        }
    }

    public function setConnection($connection): void
    {
        $this->connection = $connection;
    }

    public function find($id)
    {
    }

    public function query($query)
    {
    }

    public function update($id, $data)
    {
    }

    public function save($data)
    {
    }

    public function remove($id)
    {
    }

    /**
     * Realiza as request para a API do Elasticsearch
     *
     * @param string $resource  Rota da API a ser chamada
     * @param mixed $payload    Conteúdo a ser enviado
     * @param string $method    Método HTTP da requisição
     * @return array
     */
    public function sendRequest(string $resource, $payload = null, string $method = 'POST'): array
    {
        $payload = (is_array($payload)) ? wp_json_encode($payload) : $payload;
        $endpoint = "{$this->endpoint}{$resource}";
        $config = [
            'method'        => $method,
            'body'          => $payload,
            'headers'       => [
                'Content-Type'      => 'application/json',
                'Accept'            => 'application/json',
            ],
        ];
        if (defined('ELASTICSEARCH_USER') && defined('ELASTICSEARCH_PASS')) {
            $config['headers']['Authorization'] = 'Basic ' . base64_encode(ELASTICSEARCH_USER . ':' . ELASTICSEARCH_PASS);
        }

        $response = wp_remote_request($endpoint, $config);
        if (is_wp_error($response)) throw new Exception('Elasticsearch Error: ' . $response->get_error_message());
        $body = wp_remote_retrieve_body($response);
        $decodedBody = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) return $body;
        return $decodedBody;
    }

    /**
     * Indexa um documento no Elasticsearch
     *
     * @param mixed $index  Payload com os dados do documento a ser indexado
     * @param string $index Nome do índice
     * @return array
     */
    public function indexing($payload, string $index): array
    {
        return $this->sendRequest($index, $payload, 'POST');
    }

    /**
     * Realiza uma busca no Elasticsearch
     *
     * @param array $query  Payload com a query a ser executada
     * @param string $index Nome do índice
     * @return array
     */
    public function search(array $query, string $index): array
    {
        return $this->sendRequest("{$index}/_search", $query);
    }

    /**
     * Realiza uma busca no Elasticsearch
     *
     * @param string $query  Payload com a query a ser executada
     * @param string $index Nome do índice
     * @return array
     */
    public function searchRaw(string $query, string $index): array
    {
        return $this->sendRequest("{$index}/_search", $query);
    }

    /**
     * Cria o Mapping de um Type no Elasticsearch
     *
     * @param string $index Nome do índice
     * @param array $properties
     * @return array
     */
    public function mappingIndex(string $index, array $properties): array
    {
        $payload = ["mappings" => []];
        $payload['mappings']['properties'] = $properties;
        return $this->sendRequest($index, $payload, 'PUT');
    }

    /**
     * Remove o índice atual no Elasticsearch
     *
     * @param string $index Nome do índice
     * @return array
     */
    public function removeIndex(string $index): array
    {
        return $this->sendRequest($index, [], 'DELETE');
    }

    public function getHits(array $result): array
    {
        return $result['hits']['hits'];
    }
}
