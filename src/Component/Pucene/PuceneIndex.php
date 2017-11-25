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

    public function __construct(string $name, StorageInterface $storage, AnalyzerInterface $analyzer)
    {
        $this->name = $name;
        $this->storage = $storage;
        $this->analyzer = $analyzer;
    }

    public function index(array $document, string $type, ?string $id = null): array
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

    public function delete(string $type, string $id): void
    {
        $this->storage->deleteDocument($id);
    }

    public function search(Search $search, $type): array
    {
        if (is_string($type)) {
            $type = [$type];
        }

        $documents = $this->storage->search($search, $type);

        $hits = [];
        $maxScore = null;
        foreach ($documents as $document) {
            $hits[] = $document->toArray();

            if ($document->getScore() && $maxScore < $document->getScore()) {
                $maxScore = $document->getScore();
            }
        }

        // TODO total

        return [
            'total' => '?',
            'hits' => $hits,
            'max_score' => $maxScore,
        ];
    }

    public function get(string $type, string $id): array
    {
        return $this->storage->get($type, $id);
    }
}
