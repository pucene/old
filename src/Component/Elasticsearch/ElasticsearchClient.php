<?php

namespace Pucene\Component\Elasticsearch;

use Elasticsearch\Client;
use Pucene\Component\Client\ClientInterface;
use Pucene\Component\Elasticsearch\QueryBuilder\SearchBuilder;

class ElasticsearchClient implements ClientInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var SearchBuilder
     */
    private $searchBuilder;
    /**
     * @var array
     */
    private $adapterConfig;

    /**
     * @param Client $client
     * @param SearchBuilder $searchBuilder
     * @param array $adapterConfig
     */
    public function __construct(Client $client, SearchBuilder $searchBuilder, array $adapterConfig)
    {
        $this->client = $client;
        $this->searchBuilder = $searchBuilder;
        $this->adapterConfig = $adapterConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return new ElasticsearchIndex($name, $this->client, $this->searchBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, array $parameters)
    {
        $parameters['settings']['index'] = $this->adapterConfig['settings'];
        $response = $this->client->indices()->create(['index' => $name, 'body' => $parameters]);

        if (!$response['acknowledged']) {
            throw new \Exception();
        }

        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        return $this->client->indices()->delete(['index' => $name]);
    }
}
