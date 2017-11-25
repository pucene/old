<?php

namespace Pucene\Component\Pucene;

interface StorageFactoryInterface
{
    public function create(string $name): StorageInterface;
}
