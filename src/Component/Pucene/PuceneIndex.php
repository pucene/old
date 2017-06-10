<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Client\IndexInterface;
use Pucene\Component\Pucene\Model\Analysis;
use Pucene\Component\Pucene\Model\Document;
use Pucene\Component\Pucene\Model\Field;
use Pucene\Component\QueryBuilder\Search;
use Ramsey\Uuid\Uuid;

class PuceneIndex implements IndexInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @param string $name
     * @param StorageInterface $storage
     * @param AnalyzerInterface $analyzer
     */
    public function __construct($name, StorageInterface $storage, AnalyzerInterface $analyzer)
    {
        $this->name = $name;
        $this->storage = $storage;
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritdoc}
     */
    public function index(array $document, $type, $id = null)
    {
        if ($id) {
            $this->delete($type, $id);
        }

        $fields = [];
        foreach ($document as $fieldName => $fieldContent) {
            $fields[$fieldName] = new Field($fieldName, $this->analyzer->analyze($fieldContent));
        }

        $id = $id ?: Uuid::uuid4()->toString();
        $analysis = new Analysis(new Document($id, $type, $this->name, $document), $fields);
        $this->storage->saveDocument($analysis);

        return $analysis->getDocument()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($type, $id)
    {
        $this->storage->deleteDocument($id);
    }

    /**
     * {@inheritdoc}
     */
    public function search(Search $search, $type)
    {
        if (is_string($type)) {
            $type = [$type];
        }

        $documents = $this->storage->search($search, $type);

        return [
            'hits' => array_map(
                function (Document $document) {
                    return $document->toArray();
                },
                $documents
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get($type, $id)
    {
        return $this->storage->get($type, $id);
    }

    public function optimize()
    {
        $this->storage->optimize();
    }
}
