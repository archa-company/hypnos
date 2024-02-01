<?php

namespace Morpheus\Modules\Core\Classes\ElasticSearch;

class ModelMorpheus extends AbstractModel
{

    public static function factory($post)
    {
        return new self($post);
    }

    public function execute()
    {
        $result = new ResultCard();
        $result->id = (int) $this->post->externalId;
        $result->site = $this->post->site;
        $result->hat = $this->post->hat;
        $result->citySign = $this->post->citySign;
        $result->postLayout = $this->post->postLayout;
        $result->advertisingNews = $this->post->advertisingNews;
        $result->title = $this->post->title;
        $result->image = $this->post->thumbnail;
        $result->link = $this->post->uri;
        $result->type = $this->post->type;
        $result->uuid = $this->post->uuid;
        $result->createdAt = $this->post->createdAt;
        $result->updatedAt = $this->post->updatedAt;
        return $result;
    }
}
