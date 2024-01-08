<?php

namespace Morpheus\Shared\Clients;

use Morpheus\Contracts\ClientInterface;

class S3 implements ClientInterface
{
    public $connection;

    public function setConnection($connection): void
    {
        $this->connection = $connection;
    }

    public function find($id)
    {
    }

    public function query($query)
    {
    }

    public function update($id, $data)
    {
    }

    public function save($data)
    {
    }

    public function remove($id)
    {
    }
}
