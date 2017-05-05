<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\Compound;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\Compound\BoolQuery;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\Symfony\Pool\PoolInterface;

class BoolVisitor implements VisitorInterface
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
     * {@inheritdoc}
     *
     * @param BoolQuery $query
     */
    public function visit(QueryInterface $query)
    {
        $parameter = [];

        if (0 < count($query->getShouldQueries())) {
            $parameter['should'] = $this->visitQueries($query->getShouldQueries());
        }

        if (0 < count($query->getFilterQueries())) {
            $parameter['filter'] = $this->visitQueries($query->getFilterQueries());
        }

        if (0 < count($query->getMustNotQueries())) {
            $parameter['must_not'] = $this->visitQueries($query->getMustNotQueries());
        }

        if (0 < count($query->getMustQueries())) {
            $parameter['must'] = $this->visitQueries($query->getMustQueries());
        }

        return ['bool' => $parameter];
    }

    /**
     * Returns visited queries.
     *
     * @param QueryInterface[] $queries
     *
     * @return array
     */
    private function visitQueries(array $queries)
    {
        $result = [];
        foreach ($queries as $query) {
            $result[] = $this->getInterpreter($query)->visit($query);
        }

        return $result;
    }

    /**
     * @param QueryInterface $query
     *
     * @return VisitorInterface
     */
    private function getInterpreter(QueryInterface $query)
    {
        return $this->interpreterPool->get(get_class($query));
    }
}
