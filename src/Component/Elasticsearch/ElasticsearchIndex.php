<?php

namespace Pucene\Component\Elasticsearch;

use Elasticsearch\Client;
use Pucene\Component\Client\IndexInterface;
use Pucene\Component\Elasticsearch\QueryBuilder\SearchBuilder;
use Pucene\Component\QueryBuilder\Search;

class ElasticsearchIndex implements IndexInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @param string $name
     * @param Client $client
     * @param SearchBuilder $searchBuilder
     */
    public function __construct($name, Client $client, SearchBuilder $searchBuilder)
    {
        $this->name = $name;
        $this->client = $client;
        $this->searchBuilder = $searchBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function index(array $document, $type, $id = null)
    {
        $parameters = ['type' => $type, 'index' => $this->name, 'body' => $document];
        if ($id) {
            $parameters['id'] = $id;
        }

        return $this->client->index($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($type, $id)
    {
        return $this->client->delete(['id' => $id, 'index' => $this->name, 'type' => $type]);
    }

    /**
     * {@inheritdoc}
     */
    public function search(Search $search, $type)
    {
        $response = $this->client->search(
            [
                'index' => $this->name,
                'type' => $type,
                'body' => [
                    'query' => $this->searchBuilder->build($search)->toArray(),
                ],
            ]
        );

        return $response['hits'];
    }
}
