<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter;

use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;
use Pucene\Component\Pucene\Mapping\Mapping;
use Pucene\Component\Pucene\Model\Document;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Component\Symfony\Pool\PoolInterface;

class DbalInterpreter
{
    /**
     * @var PoolInterface
     */
    private $interpreterPool;

    /**
     * @var Mapping
     */
    private $mapping;

    public function __construct(PoolInterface $interpreterPool, Mapping $mapping)
    {
        $this->interpreterPool = $interpreterPool;
        $this->mapping = $mapping;
    }

    private function getQueryBuilder(
        array $types,
        DbalStorage $storage,
        ElementInterface $element
    ): PuceneQueryBuilder {
        $connection = $storage->getConnection();
        $schema = $storage->getSchema();

        $queryBuilder = (new PuceneQueryBuilder($connection, $schema))
            ->select('document.*')
            ->from($schema->getDocumentsTableName(), 'document')
            ->where('document.type IN (?)')
            ->setParameter(0, implode(',', $types));

        /** @var InterpreterInterface $interpreter */
        $interpreter = $this->interpreterPool->get(get_class($element));
        $expression = $interpreter->interpret($element, $queryBuilder, $storage->getName());
        if ($expression) {
            $queryBuilder->andWhere($expression);
        }

        return $queryBuilder;
    }

    /**
     * @return Document[]
     */
    public function interpret(array $types, Search $search, DbalStorage $storage, ElementInterface $element): array
    {
        $queryBuilder = $this->getQueryBuilder($types, $storage, $element)
            ->setMaxResults($search->getSize())
            ->setFirstResult($search->getFrom());

        /** @var InterpreterInterface $interpreter */
        $interpreter = $this->interpreterPool->get(get_class($element));
        $expression = null;
        if (0 === count($search->getSorts())) {
            $scoringAlgorithm = new ScoringAlgorithm($queryBuilder, $storage->getSchema(), $this->interpreterPool);
            $expression = $interpreter->scoring($element, $scoringAlgorithm, $storage->getName());
        }

        if ($expression) {
            $queryBuilder->addSelect('(' . $expression . ') as score')->orderBy('score', 'desc');
        }

        if (0 < count($search->getSorts())) {
            foreach ($search->getSorts() as $sort) {
                if ('_uid' === $sort->getField()) {
                    $queryBuilder->addOrderBy('id', $sort->getOrder());

                    continue;
                }

                $type = $this->mapping->getTypeForField($storage->getName(), $sort->getField());
                $field = $queryBuilder->joinValue($sort->getField(), $type);
                $queryBuilder->addOrderBy($field . '.value', $sort->getOrder());
            }
        }

        $result = [];
        foreach ($queryBuilder->execute()->fetchAll() as $row) {
            $result[] = new Document(
                $row['id'],
                $row['type'],
                $storage->getName(),
                json_decode($row['document'], true), array_key_exists('score', $row) ? (float) $row['score'] : null
            );
        }

        return $result;
    }

    public function count(array $types, DbalStorage $storage, ElementInterface $element): int
    {
        $queryBuilder = $this->getQueryBuilder($types, $storage, $element)
            ->select('COUNT(document.id)');

        return (int) $queryBuilder->execute()->fetchColumn();
    }
}
