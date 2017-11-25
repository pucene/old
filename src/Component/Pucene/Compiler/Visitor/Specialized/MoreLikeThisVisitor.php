<?php

namespace Pucene\Component\Pucene\Compiler\Visitor\Specialized;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Pucene\Compiler\Element\BoolElement;
use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\Element\IdsElement;
use Pucene\Component\Pucene\Compiler\Element\NotElement;
use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Compiler\VisitorInterface;
use Pucene\Component\Pucene\PuceneClient;
use Pucene\Component\Pucene\StorageInterface;
use Pucene\Component\Pucene\TermStatisticsInterface;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\ArtificialDocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThisQuery;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\TextLike;

class MoreLikeThisVisitor implements VisitorInterface
{
    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @var PuceneClient
     */
    private $client;

    public function __construct(AnalyzerInterface $analyzer, PuceneClient $client)
    {
        $this->analyzer = $analyzer;
        $this->client = $client;
    }

    /**
     * @param MoreLikeThisQuery $query
     */
    public function visit(QueryInterface $query, StorageInterface $storage): ?ElementInterface
    {
        $terms = $this->getTerms($query, $storage->termStatistics());

        $elements = [];
        foreach ($query->getFields() as $field) {
            foreach (array_slice($terms[$field], 0, $query->getMaxQueryTerms()) as $term => $attributes) {
                $elements[] = new TermElement($field, $term);
            }
        }

        if (0 === count($elements)) {
            return null;
        }

        $mustNotElements = $this->getMustNotElements($query->getLike());
        if (0 === count($mustNotElements)) {
            return new CompositeElement(CompositeElement:: OR, $elements);
        }

        return new BoolElement(
            new CompositeElement(
                CompositeElement:: AND,
                [
                    new CompositeElement(CompositeElement:: AND, $mustNotElements),
                    new CompositeElement(CompositeElement:: OR, $elements),
                ]
            ),
            $elements
        );
    }

    private function getTerms(MoreLikeThisQuery $query, TermStatisticsInterface $termStatistics): array
    {
        $terms = [];
        foreach ($query->getLike() as $like) {
            if ($like instanceof TextLike) {
                $this->likeText($query, $like, $terms);
            } elseif ($like instanceof DocumentLike) {
                $this->likeDocument($query, $like, $terms);
            } elseif ($like instanceof ArtificialDocumentLike) {
                $this->likeArtificialDocument($query, $like, $terms);
            }
        }

        $result = [];
        foreach ($query->getFields() as $field) {
            $result[$field] = [];

            foreach ($terms[$field] as $term => $parameter) {
                $frequency = $termStatistics->documentCount($field, $term);

                if ($parameter['count'] < $query->getMinTermFreq() || $frequency < $query->getMinDocFreq()) {
                    continue;
                }

                $idf = $termStatistics->inverseDocumentFrequency($field, $term);
                $result[$field][$term] = [
                    'idf' => $idf,
                    'count' => $parameter['count'],
                    'complete' => $idf * $parameter['count'],
                ];
            }
            uasort(
                $result[$field],
                function($a, $b) {
                    return $a['idf'] <=> $b['idf'];
                }
            );
            $result[$field] = array_reverse($result[$field]);
        }

        return $result;
    }

    private function likeText(MoreLikeThisQuery $query, TextLike $like, array &$terms): void
    {
        foreach ($query->getFields() as $field) {
            $this->analyzeText($field, $like->getText(), $terms);
        }
    }

    private function likeDocument(MoreLikeThisQuery $query, DocumentLike $like, array &$terms): void
    {
        $index = $this->client->get($like->getIndex());
        $document = $index->get($like->getType(), $like->getId());

        foreach ($query->getFields() as $field) {
            $this->analyzeText($field, $document['_source'][$field], $terms);
        }
    }

    private function likeArtificialDocument(MoreLikeThisQuery $query, ArtificialDocumentLike $like, array &$terms): void
    {
        foreach ($query->getFields() as $field) {
            $this->analyzeText($field, $like->getDocument()[$field], $terms);
        }
    }

    private function analyzeText(string $field, string $text, array &$terms): void
    {
        $tokens = $this->analyzer->analyze($text);

        if (!array_key_exists($field, $terms)) {
            $terms[$field] = [];
        }

        foreach ($tokens as $token) {
            if (!array_key_exists($token->getEncodedTerm(), $terms[$field])) {
                $terms[$field][$token->getEncodedTerm()] = ['count' => 0];
            }

            ++$terms[$field][$token->getEncodedTerm()]['count'];
        }
    }

    private function getMustNotElements(array $likes): array
    {
        $ids = [];
        foreach ($likes as $like) {
            if ($like instanceof DocumentLike) {
                $ids[] = $like->getId();
            }
        }

        if (0 === count($ids)) {
            return [];
        }

        return [new NotElement(new IdsElement($ids))];
    }
}
