<?php

namespace Pucene\Component\Client;

interface ClientInterface
{
    /**
     * @param string $name
     *
     * @return IndexInterface
     */
    public function get($name);

    /**
     * @param string $name
     * @param array $parameters
     *
     * @return IndexInterface
     */
    public function create($name, array $parameters);

    /**
     * @param string $name
     */
    public function delete($name);
}
