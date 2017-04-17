<?php

namespace Pucene\Component\Elasticsearch;

use Elasticsearch\Client;
use Pucene\Component\Client\IndexInterface;
use Pucene\Component\Elasticsearch\QueryBuilder\SearchBuilder;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Component\QueryBuilder\Sort\IdSort;

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
        $parameter = [
            'index' => $this->name,
            'type' => $type,
            'body' => [
                'size' => $search->getSize(),
                'from' => $search->getFrom(),
                'query' => $this->searchBuilder->build($search)->toArray(),
            ],
        ];

        if (0 < count($search->getSorts())) {
            $parameter['body']['sort'] = [];
            foreach ($search->getSorts() as $sort) {
                if ($sort instanceof IdSort) {
                    $parameter['body']['sort']['_uid'] = $sort->getOrder();
                }
            }
        }

        $response = $this->client->search($parameter);

        return $response['hits'];
    }

    /**
     * {@inheritdoc}
     */
    public function get($type, $id)
    {
        $response = $this->client->get(
            [
                'index' => $this->name,
                'type' => $type,
                'id' => $id,
            ]
        );

        // TODO pucene version
        unset($response['_version'], $response['found']);

        return $response;
    }
}
