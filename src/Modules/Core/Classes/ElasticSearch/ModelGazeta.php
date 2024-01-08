<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

class ModelGazeta extends AbstractModel
{

    public static function factory($post, string $baseUrl = null)
    {
        return new self($post, $baseUrl);
    }

    public function execute()
    {
        $result = new ResultCard();
        $result->id = (int) preg_replace('/[\D]*/', '', $this->post->externalId);
        $result->site = $this->post->site;
        $result->hat = $this->post->caption;
        $result->title = $this->post->title;
        $result->image = $this->post->mainImageSourceUrl;
        $result->link = "https://{$this->baseUrl}{$this->post->url}";
        $result->type = $this->post->type;
        $result->uuid = $this->post->guid;
        $result->createdAt = $this->post->publishedAt;
        $result->updatedAt = $this->post->updatedAt;
        return $result;
    }
}
