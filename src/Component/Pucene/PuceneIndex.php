<?php

namespace Pucene\Component\Pucene;

use Pucene\Component\Client\IndexInterface;
use Pucene\Component\Mapping\Types;
use Pucene\Component\Pucene\Mapping\Mapping;
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
     * @var Mapping
     */
    private $mapping;

    public function __construct(string $name, StorageInterface $storage, Mapping $mapping)
    {
        $this->name = $name;
        $this->storage = $storage;
        $this->mapping = $mapping;
    }

    public function index(array $document, string $type, ?string $id = null): array
    {
        if ($id) {
            $this->delete($type, $id);
        }

        $fields = [];
        $normalized = $this->normalizeObject($document);
        foreach ($normalized as $fieldName => $fieldContent) {
            $fieldType = $this->mapping->getTypeForField($this->name, $fieldName);
            if (!$fieldType || Types::BINARY === $fieldType) {
                continue;
            }

            $tokens = $this->analyze($fieldName, $fieldContent);

            $fields[$fieldName] = new Field($fieldName, $tokens, $fieldContent, $fieldType);
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

    public function get(?string $type, string $id): array
    {
        return $this->storage->get($type, $id);
    }

    private function analyze(string $fieldName, array $fieldContent)
    {
        $analyzer = $this->mapping->getAnalyzerForField($this->name, $fieldName);
        if (!$analyzer) {
            return [];
        }

        $tokens = [];
        foreach ($fieldContent as $content) {
            if (!$content) {
                continue;
            }

            $tokens = array_merge($tokens, $analyzer->analyze($content));
        }

        return $tokens;
    }

    private function normalizeObject(array $object, string $prefix = '')
    {
        $result = [];
        foreach ($object as $key => $value) {
            $prefixedKey = is_int($key) ? $prefix : $prefix . $key . '.';
            if (is_array($value)) {
                $normalizedValue = $this->normalizeObject($value, $prefixedKey);
                $result = array_merge_recursive($result, $normalizedValue);

                continue;
            }

            foreach ($this->mapping->getFieldNames($this->name, rtrim($prefixedKey, '.')) as $fieldName) {
                if (!array_key_exists($fieldName, $result)) {
                    $result[$fieldName] = [];
                }

                $result[$fieldName][] = $value;
            }
        }

        return $result;
    }
}
