<?php

namespace Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis;

use Pucene\Component\QueryBuilder\Query\QueryInterface;

class MoreLikeThisQuery implements QueryInterface
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

    public function __construct(array $like, array $fields = [])
    {
        $this->like = $like;
        $this->fields = $fields;
    }

    public function getLike(): array
    {
        return $this->like;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getMaxQueryTerms(): int
    {
        return $this->maxQueryTerms;
    }

    public function setMaxQueryTerms(int $maxQueryTerms): self
    {
        $this->maxQueryTerms = $maxQueryTerms;

        return $this;
    }

    public function getMinTermFreq(): int
    {
        return $this->minTermFreq;
    }

    public function setMinTermFreq(int $minTermFreq): self
    {
        $this->minTermFreq = $minTermFreq;

        return $this;
    }

    public function getMinDocFreq(): int
    {
        return $this->minDocFreq;
    }

    public function setMinDocFreq(int $minDocFreq): self
    {
        $this->minDocFreq = $minDocFreq;

        return $this;
    }
}
