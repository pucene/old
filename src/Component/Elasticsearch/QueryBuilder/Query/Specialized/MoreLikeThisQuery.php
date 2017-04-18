<?php

namespace Pucene\Component\Elasticsearch\QueryBuilder\Query\Specialized;

use Pucene\Component\Elasticsearch\QueryBuilder\QueryInterface;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\ArtificialDocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThis;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\TextLike;

/**
 * Represents more_like_this query.
 */
class MoreLikeThisQuery implements QueryInterface
{
    /**
     * @var MoreLikeThis
     */
    private $query;

    /**
     * @param MoreLikeThis $query
     */
    public function __construct(MoreLikeThis $query)
    {
        $this->query = $query;
    }

    public function toArray()
    {
        $like = [];
        foreach ($this->query->getLike() as $item) {
            if ($item instanceof TextLike) {
                $like[] = $item->getText();
            } elseif ($item instanceof DocumentLike) {
                $like[] = ['_index' => $item->getIndex(), '_type' => $item->getType(), '_id' => $item->getId()];
            } elseif ($item instanceof ArtificialDocumentLike) {
                $like[] = ['_index' => $item->getIndex(), '_type' => $item->getType(), 'doc' => $item->getDocument()];
            }
        }

        if (1 === count($like)) {
            $like = reset($like);
        }

        $parameters = [
            'like' => $like,
            'min_term_freq' => $this->query->getMinTermFrequency(),
            'min_doc_freq' => $this->query->getMinDocFreq(),
            'minimum_should_match' => '0%',
        ];
        if (count($this->query->getFields())) {
            $parameters['fields'] = $this->query->getFields();
        }

        return ['more_like_this' => $parameters];
    }
}
