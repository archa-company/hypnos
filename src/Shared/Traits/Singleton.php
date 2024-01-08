<?php

namespace Morpheus\Shared\Traits;

trait Singleton
{
    /**
     * Holds the singleton instance of this class
     * @since 2.3.3
     * @var self
     */
    static $instance = false;
    public static function getInstance()
    {
        if (self::$instance) return self::$instance;
        self::$instance = new self;
        self::$instance->init();
        return self::$instance;
    }
}
