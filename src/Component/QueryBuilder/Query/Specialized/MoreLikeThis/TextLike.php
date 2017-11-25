<?php

namespace Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis;

class TextLike
{
    /**
     * @var string
     */
    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
