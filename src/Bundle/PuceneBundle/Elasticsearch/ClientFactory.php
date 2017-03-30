<?php

namespace Pucene\Bundle\PuceneBundle\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ClientFactory
{
    /**
     * @param array $config
     *
     * @return Client
     */
    public static function create($config)
    {
        return ClientBuilder::create()->setHosts($config['hosts'])->build();
    }
}
