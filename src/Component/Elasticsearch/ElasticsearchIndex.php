<?php

namespace Pucene\Component\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Pucene\Component\Client\IndexInterface;
use Pucene\Component\Elasticsearch\Compiler\Compiler;
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
     * @var Compiler
     */
    private $compiler;

    public function __construct(string $name, Client $client, Compiler $compiler)
    {
        $this->name = $name;
        $this->client = $client;
        $this->compiler = $compiler;
    }

    public function index(array $document, string $type, ?string $id = null): array
    {
        $parameters = ['type' => $type, 'index' => $this->name, 'body' => $document];
        if ($id) {
            $parameters['id'] = $id;
        }

        return $this->client->index($parameters);
    }

    public function delete(string $type, string $id): void
    {
        $this->client->delete(['id' => $id, 'index' => $this->name, 'type' => $type]);
    }

    public function search(Search $search, $type): array
    {
        $parameter = [
            'index' => $this->name,
            'type' => $type,
            'body' => [
                'size' => $search->getSize(),
                'from' => $search->getFrom(),
                'query' => $this->compiler->compile($search->getQuery()),
            ],
        ];

        if (0 < count($search->getSorts())) {
            $parameter['body']['sort'] = [];
            foreach ($search->getSorts() as $sort) {
                $parameter['body']['sort'][$sort->getField()] = $sort->getOrder();
            }
        }

        $response = $this->client->search($parameter);

        return $response['hits'];
    }

    public function count(Search $search, $type): int
    {
        $parameter = [
            'index' => $this->name,
            'type' => $type,
            'body' => [
                'query' => $this->compiler->compile($search->getQuery()),
            ],
        ];

        $response = $this->client->count($parameter);

        return $response['count'];
    }

    public function get(?string $type, string $id): array
    {
        try {
            $response = $this->client->get(
                [
                    'index' => $this->name,
                    'type' => $type,
                    'id' => $id,
                ]
            );

            // TODO pucene version
            unset($response['_version']);

            return $response;
        } catch (Missing404Exception $exception) {
            return ['_index' => $this->name, '_type' => $type, '_id' => $id, 'found' => false];
        }
    }
}
