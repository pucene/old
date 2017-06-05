<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter;

use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;
use Pucene\Component\Pucene\Model\Document;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Component\QueryBuilder\Sort\IdSort;
use Pucene\Component\QueryBuilder\Sort\ScoreSort;
use Pucene\Component\Symfony\Pool\PoolInterface;
use Pucene\Component\Utils\SortUtils;

class DbalInterpreter
{
    public static $sortPaths = [
        ScoreSort::class => 'score',
        IdSort::class => 'id',
    ];

    /**
     * @var PoolInterface
     */
    private $interpreterPool;

    /**
     * @param PoolInterface $interpreterPool
     */
    public function __construct(PoolInterface $interpreterPool)
    {
        $this->interpreterPool = $interpreterPool;
    }

    /**
     * @param array $types
     * @param Search $search
     * @param ElementInterface $element
     * @param DbalStorage $storage
     *
     * @return Document[]
     */
    public function interpret(array $types, Search $search, DbalStorage $storage, ElementInterface $element)
    {
        $connection = $storage->getConnection();
        $schema = $storage->getSchema();

        $queryBuilder = (new PuceneQueryBuilder($connection, $storage->getSchema()))
            ->select('document.*')
            ->from($schema->getDocumentsTableName(), 'document')
            ->where('document.type IN (?)')
            ->setParameter(0, implode(',', $types));

        /** @var InterpreterInterface $interpreter */
        $interpreter = $this->interpreterPool->get(get_class($element));
        $expression = $interpreter->interpret($element, $queryBuilder);
        if ($expression) {
            $queryBuilder->andWhere($expression);
        }

        $scoringAlgorithm = new ScoringAlgorithm($queryBuilder, $schema, $this->interpreterPool);

        $result = [];
        foreach ($queryBuilder->execute()->fetchAll() as $row) {
            $result[] = new Document(
                $row['id'],
                $row['type'],
                $storage->getName(),
                json_decode($row['document'], true),
                $interpreter->newScoring($element, $scoringAlgorithm, $row)
            );
        }

        $paths = [];
        foreach ($search->getSorts() as $sort) {
            $paths[] = self::$sortPaths[get_class($sort)];
        }

        $result = SortUtils::multisort($result, $paths);

        return array_splice($result, $search->getFrom(), $search->getSize());
    }
}
