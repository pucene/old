<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder;

use Pucene\Component\QueryBuilder\Search;

/**
 * Builder for search.
 */
class SearchBuilder
{
    /**
     * @var QueryBuilderPoolInterface
     */
    private $builders;

    /**
     * @param QueryBuilderPoolInterface $builders
     */
    public function __construct(QueryBuilderPoolInterface $builders)
    {
        $this->builders = $builders;
    }

    /**
     * @param Search $search
     *
     * @return QueryInterface
     */
    public function build(Search $search)
    {
        $query = $search->getQuery();

        return $this->builders->get(get_class($query))->build($query);
    }
}
