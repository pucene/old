<?php

namespace Pucene\Component\Pucene;

interface StorageFactoryInterface
{
    /**
     * @param string $name
     *
     * @return StorageInterface
     */
    public function create(string $name);
}
