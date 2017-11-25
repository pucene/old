<?php

namespace Pucene\Bundle\PuceneBundle\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ClientFactory
{
    public static function create(array $config): Client
    {
        return ClientBuilder::create()->setHosts($config['hosts'])->build();
    }
}
