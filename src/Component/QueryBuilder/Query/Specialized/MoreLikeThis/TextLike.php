<?php

namespace Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis;

class TextLike
{
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * Returns text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
