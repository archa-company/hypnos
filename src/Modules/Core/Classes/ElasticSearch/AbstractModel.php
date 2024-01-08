<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

abstract class AbstractModel implements InterfaceModel
{
    public $post;
    public $baseUrl;

    public function __construct($post, string $baseUrl = null)
    {
        $this->post = $post;
        $this->baseUrl = $baseUrl;
    }
}
