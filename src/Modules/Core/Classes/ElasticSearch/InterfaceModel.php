<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

interface InterfaceModel
{
    public static function factory($post);
    public function execute();
}
