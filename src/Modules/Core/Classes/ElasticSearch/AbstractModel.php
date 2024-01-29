<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

abstract class AbstractModel implements InterfaceModel
{
    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }
}
