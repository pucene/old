<?php

namespace Pucene\Component\Elasticsearch;

use Elasticsearch\Client;
use Pucene\Component\Client\ClientInterface;
use Pucene\Component\Client\IndexInterface;
use Pucene\Component\Elasticsearch\Compiler\Compiler;

class ElasticsearchClient implements ClientInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Compiler
     */
    private $compiler;
    /**
     * @var array
     */
    private $adapterConfig;

    public function __construct(Client $client, Compiler $compiler, array $adapterConfig)
    {
        $this->client = $client;
        $this->compiler = $compiler;
        $this->adapterConfig = $adapterConfig;
    }

    public function get(string $name): IndexInterface
    {
        return new ElasticsearchIndex($name, $this->client, $this->compiler);
    }

    public function create(string $name, array $parameters): IndexInterface
    {
        $parameters['settings']['index'] = $this->adapterConfig['settings'];
        $parameters = $this->filterArray($parameters);
        $response = $this->client->indices()->create(['index' => $name, 'body' => $parameters]);

        if (!$response['acknowledged']) {
            throw new \Exception();
        }

        return $this->get($name);
    }

    public function delete(string $name): void
    {
        $this->client->indices()->delete(['index' => $name]);
    }

    private function filterArray(array $input): array
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->filterArray($value);
            }
        }

        return array_filter($input);
    }
}
