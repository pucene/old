<?php

namespace Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MoreLikeThis implements QueryInterface
{
    /**
     * @var array
     */
    private $like;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @param array $like
     * @param array $fields
     */
    public function __construct(array $like, array $fields = [])
    {
        $this->like = $like;
        $this->fields = $fields;
    }

    /**
     * Returns like.
     *
     * @return array
     */
    public function getLike(): array
    {
        return $this->like;
    }

    /**
     * Returns fields.
     *
     * @return \string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
