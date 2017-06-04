<?php

namespace Pucene\Component\ZendSearch;

use Pucene\Component\Client\ClientInterface;
use Symfony\Component\Filesystem\Filesystem;
use ZendSearch\Lucene\Index;

class ZendSearchClient implements ClientInterface
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param string $directory
     * @param Filesystem $filesystem
     */
    public function __construct($directory, Filesystem $filesystem)
    {
        $this->directory = $directory;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return new ZendSearchIndex($name, new Index($this->directory . DIRECTORY_SEPARATOR . $name));
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, array $parameters)
    {
        $this->filesystem->mkdir($this->directory . DIRECTORY_SEPARATOR . $name);

        return new ZendSearchIndex($name, new Index($this->directory . DIRECTORY_SEPARATOR . $name, true));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        $this->filesystem->remove($this->directory . DIRECTORY_SEPARATOR . $name);
    }
}
