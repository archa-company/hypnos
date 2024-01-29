<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

class Mapper
{
    private string $model;
    private array $hits;
    private array $result = [];

    public function __construct(array $hits, string $model)
    {
        $this->hits = $hits;
        $this->model = $model;
    }

    public static function factory(array $hits, string $model)
    {
        return new self($hits, $model);
    }

    public function process()
    {
        foreach ($this->hits as $hit) {
            $post = $hit->_source;
            // dd($post);
            array_push($this->result, $this->model::factory($post)->execute());
        }
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }
}
