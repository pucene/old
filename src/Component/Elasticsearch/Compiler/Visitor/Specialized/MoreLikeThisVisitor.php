<?php

namespace Pucene\Component\Elasticsearch\Compiler\Visitor\Specialized;

use Pucene\Component\Elasticsearch\Compiler\VisitorInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\ArtificialDocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThisQuery;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\TextLike;

class MoreLikeThisVisitor implements VisitorInterface
{
    /**
     * @param MoreLikeThisQuery $query
     */
    public function visit(QueryInterface $query): array
    {
        $like = [];
        foreach ($query->getLike() as $item) {
            if ($item instanceof TextLike) {
                $like[] = $item->getText();
            } elseif ($item instanceof DocumentLike) {
                $like[] = array_filter(
                    ['_index' => $item->getIndex(), '_type' => $item->getType(), '_id' => $item->getId()]
                );
            } elseif ($item instanceof ArtificialDocumentLike) {
                $like[] = ['_index' => $item->getIndex(), '_type' => $item->getType(), 'doc' => $item->getDocument()];
            }
        }

        if (1 === count($like)) {
            $like = reset($like);
        }

        $parameters = [
            'like' => $like,
            'max_query_terms' => $query->getMaxQueryTerms(),
            'min_term_freq' => $query->getMinTermFreq(),
            'min_doc_freq' => $query->getMinDocFreq(),
            'minimum_should_match' => '0%',
        ];

        if (count($query->getFields())) {
            $parameters['fields'] = $query->getFields();
        }

        return ['more_like_this' => $parameters];
    }
}
