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
     * @var int
     */
    private $minTermFrequency = 2;

    /**
     * @var int
     */
    private $minDocFreq = 5;

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

    /**
     * Returns minTermFrequency.
     *
     * @return int
     */
    public function getMinTermFrequency(): int
    {
        return $this->minTermFrequency;
    }

    /**
     * Set minTermFrequency.
     *
     * @param int $minTermFrequency
     *
     * @return self
     */
    public function setMinTermFrequency(int $minTermFrequency): MoreLikeThis
    {
        $this->minTermFrequency = $minTermFrequency;

        return $this;
    }

    /**
     * Returns minDocFreq.
     *
     * @return int
     */
    public function getMinDocFreq(): int
    {
        return $this->minDocFreq;
    }

    /**
     * Set minDocFreq.
     *
     * @param int $minDocFreq
     *
     * @return self
     */
    public function setMinDocFreq(int $minDocFreq): MoreLikeThis
    {
        $this->minDocFreq = $minDocFreq;

        return $this;
    }
}
