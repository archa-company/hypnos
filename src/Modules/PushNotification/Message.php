<?php

namespace Morpheus\Modules\PushNotification;

use Morpheus\Shared\Traits\UseConfig;

class Message
{

    use UseConfig;

    public $data;
    public $provider;
    public $title;
    public $message;
    public $url;
    public $image;
    public $schedule;

    public function __construct(array $data = [])
    {
        if (empty($data)) return $this;
        $this->setData($data);
        if (!empty($this->data->provider)) $this->setProvider($this->data->provider);
        if (!empty($this->data->title)) $this->setTitle($this->data->title);
        if (!empty($this->data->message)) $this->setMessage($this->data->message);
        if (!empty($this->data->url)) $this->setUrl($this->data->url);
        if (!empty($this->data->image)) $this->setImage($this->data->image);
        if (!empty($this->data->schedule)) $this->setSchedule($this->data->schedule);
    }

    public function setData(array $data)
    {
        $this->data = (object) $data;
        return $this;
    }

    public function setProvider(string $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    public function setUrl(string $url)
    {
        $siteDomain = get_site_url();
        $protocol = $this->getConfig('domain_https') ? 'https://' : 'http://';
        $oficialDomain = $protocol . $this->getConfig('domain_oficial');
        $this->url = str_replace($siteDomain, $oficialDomain, $url);
        $this->url .= $this->getConfig('push_url_tracking');
        return $this;
    }

    public function setImage(string $image)
    {
        $this->image = $image;
        return $this;
    }

    public function setSchedule(string $schedule)
    {
        $this->schedule = $schedule;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getSchedule()
    {
        return $this->schedule;
    }
}
