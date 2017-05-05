<?php

namespace Pucene\Component\Elasticsearch;

use Elasticsearch\Client;
use Pucene\Component\Client\ClientInterface;
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

    /**
     * @param Client $client
     * @param Compiler $compiler
     * @param array $adapterConfig
     */
    public function __construct(Client $client, Compiler $compiler, array $adapterConfig)
    {
        $this->client = $client;
        $this->compiler = $compiler;
        $this->adapterConfig = $adapterConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return new ElasticsearchIndex($name, $this->client, $this->compiler);
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
