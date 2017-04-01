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
     * @param Client $client
     * @param SearchBuilder $searchBuilder
     */
    public function __construct(Client $client, SearchBuilder $searchBuilder)
    {
        $this->client = $client;
        $this->searchBuilder = $searchBuilder;
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
