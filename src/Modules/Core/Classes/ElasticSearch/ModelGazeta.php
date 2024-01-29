<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

use Morpheus\Shared\Traits\UseConfig;

class ModelGazeta extends AbstractModel
{
    use UseConfig;

    public static function factory($post)
    {
        return new self($post);
    }

    public function execute()
    {
        $result = new ResultCard();
        $result->id = (int) preg_replace('/[\D]*/', '', $this->post->externalId);
        $result->site = $this->post->site;
        $result->hat = $this->post->caption;
        $result->title = $this->post->title;
        $result->image = $this->post->mainImageSourceUrl;
        $result->link = "https://{$this->getConfig('domain_oficial')}{$this->post->url}";
        $result->type = $this->post->type;
        $result->uuid = $this->post->guid;
        $result->createdAt = $this->post->publishedAt;
        $result->updatedAt = $this->post->updatedAt;
        return $result;
    }
}
