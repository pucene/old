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

    public function exists(string $name): bool
    {
        return $this->client->indices()->exists(['index' => $name]);
    }

    public function get(string $name): IndexInterface
    {
        return new ElasticsearchIndex($name, $this->client, $this->compiler);
    }

    public function create(string $name, array $parameters): IndexInterface
    {
        if ($this->exists($name)) {
            throw new \Exception('Index already exists');
        }

        $parameters['settings']['index'] = $this->adapterConfig['settings'];
        $parameters = $this->filterArray($parameters);
        $parameters['mappings'] = $this->prepareTypes($parameters['mappings']);
        $this->client->indices()->create(['index' => $name, 'body' => $parameters]);

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

    private function prepareTypes(array $types)
    {
        $result = [];
        foreach ($types as $name => $type) {
            $result[$name] = $type;
            $result[$name]['properties'] = $this->prepareProperties($type['properties']);
        }

        return $result;
    }

    private function prepareProperties(array $parameters)
    {
        $result = [];
        foreach ($parameters as $name => $parameter) {
            $result[$name] = $parameter;
            if (!array_key_exists('properties', $result[$name])) {
                continue;
            }

            unset($result[$name]['type']);
            $result[$name]['properties'] = $this->prepareProperties($result[$name]['properties']);
        }

        return $result;
    }
}
