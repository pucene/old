<?php

namespace Pucene\Component\Client;

interface ClientInterface
{
    public function get(string $name): IndexInterface;

    public function create(string $name, array $parameters): IndexInterface;

    public function delete(string $name): void;
}
