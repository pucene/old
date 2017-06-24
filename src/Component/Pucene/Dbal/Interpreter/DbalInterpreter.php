<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter;

use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;
use Pucene\Component\Pucene\Model\Document;
use Pucene\Component\QueryBuilder\Search;
use Pucene\Component\QueryBuilder\Sort\IdSort;
use Pucene\Component\Symfony\Pool\PoolInterface;

class DbalInterpreter
{
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
            ->setMaxResults($search->getSize())
            ->setFirstResult($search->getFrom())
            ->setParameter(0, implode(',', $types));

        /** @var InterpreterInterface $interpreter */
        $interpreter = $this->interpreterPool->get(get_class($element));
        $expression = $interpreter->interpret($element, $queryBuilder);
        if ($expression) {
            $queryBuilder->andWhere($expression);
        }

        $scoringAlgorithm = new ScoringAlgorithm($queryBuilder, $schema, $this->interpreterPool);
        $expression = $interpreter->scoring($element, $scoringAlgorithm);

        if ($expression) {
            $queryBuilder->addSelect('(' . $expression . ') as score')->orderBy('score', 'desc');
        }

        if (0 < count($search->getSorts())) {
            foreach ($search->getSorts() as $sort) {
                if ($sort instanceof IdSort) {
                    $queryBuilder->addOrderBy('id', $sort->getOrder());
                }
            }
        }

        $result = [];
        foreach ($queryBuilder->execute()->fetchAll() as $row) {
            $result[] = new Document(
                $row['id'],
                $row['type'],
                $storage->getName(),
                json_decode($row['document'], true),
                array_key_exists('score', $row) ? (float) $row['score'] : null
            );
        }

        usort(
            $result,
            function (Document $aDocument, Document $bDocument) {
                $a = $aDocument->getScore();
                $b = $bDocument->getScore();

                if ($a === $b) {
                    return 0;
                }

                return ($a < $b) ? 1 : -1;
            }
        );

        return $result;
    }
}
