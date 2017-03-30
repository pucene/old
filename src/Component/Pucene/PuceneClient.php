<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Client\ClientInterface;
use Pucene\Component\Lucene\PuceneIndex;

class PuceneClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return new PuceneIndex($name);
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, array $parameters)
    {
        // TODO create($name, array $parameters)
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        // TODO delete($name)
    }
}
