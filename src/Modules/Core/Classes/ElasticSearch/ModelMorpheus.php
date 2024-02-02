<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

class ModelMorpheus extends AbstractModel
{

    public static function factory($post, string $baseUrl = null)
    {
        return new self($post, $baseUrl);
    }

    public function execute()
    {
        $result = new ResultCard();
        $result->id = (int) $this->post->externalId;
        $result->site = $this->post->site;
        $result->hat = $this->post->hat;
        $result->title = $this->post->title;
        $result->image = $this->post->thumbnail;
        $result->link = "https://{$this->baseUrl}{$this->post->uri}";
        $result->type = $this->post->type;
        $result->uuid = $this->post->uuid;
        $result->createdAt = $this->post->createdAt;
        $result->updatedAt = $this->post->updatedAt;
        return $result;
    }
}
