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
     *
     * TODO default '_all'
     */
    private $fields;

    /**
     * @var int
     */
    private $maxQueryTerms = 25;

    /**
     * @var int
     */
    private $minTermFreq = 2;

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
     * Returns maxQueryTerms.
     *
     * @return int
     */
    public function getMaxQueryTerms(): int
    {
        return $this->maxQueryTerms;
    }

    /**
     * Set maxQueryTerms.
     *
     * @param int $maxQueryTerms
     *
     * @return self
     */
    public function setMaxQueryTerms(int $maxQueryTerms): MoreLikeThis
    {
        $this->maxQueryTerms = $maxQueryTerms;

        return $this;
    }

    /**
     * Returns minTermFrequency.
     *
     * @return int
     */
    public function getMinTermFreq(): int
    {
        return $this->minTermFreq;
    }

    /**
     * Set minTermFrequency.
     *
     * @param int $minTermFreq
     *
     * @return self
     */
    public function setMinTermFreq(int $minTermFreq): MoreLikeThis
    {
        $this->minTermFreq = $minTermFreq;

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
