<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Specialized;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Client\ClientInterface;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Compound\BoolBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\IdsBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderFactoryInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\ArtificialDocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThisQuery;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\TextLike;

/**
 * Builder for more_like_this query.
 */
class MoreLikeThisBuilderFactory implements QueryBuilderFactoryInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var AnalyzerInterface
     */
    private $analyzer;

    /**
     * @param ClientInterface $client
     * @param AnalyzerInterface $analyzer
     */
    public function __construct(ClientInterface $client, AnalyzerInterface $analyzer)
    {
        $this->client = $client;
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritdoc}
     *
     * @param MoreLikeThisQuery $query
     */
    public function build(QueryInterface $query, DbalStorage $storage)
    {
        $scoringQueryBuilder = $storage->createScoringQueryBuilder();
        $terms = $this->getTerms($query, $scoringQueryBuilder);

        $queries = [];
        foreach ($query->getFields() as $field) {
            foreach (array_slice($terms[$field], 0, $query->getMaxQueryTerms()) as $term => $attributes) {
                // $boost = $attributes['complete'] / reset($terms[$field])['complete'];
                $queries[] = new TermBuilder($field, $term);
            }
        }

        return new BoolBuilder(
            $queries,
            [],
            $this->getMustNotQueries($query->getLike()),
            [],
            $storage->getSchema(),
            $storage->getConnection()
        );
    }

    private function getTerms(MoreLikeThisQuery $query, ScoringQueryBuilder $scoringQueryBuilder)
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
                $frequency = $scoringQueryBuilder->getDocCountPerTerm($field, $term);

                if ($parameter['count'] < $query->getMinTermFreq() || $frequency < $query->getMinDocFreq()) {
                    continue;
                }

                $idf = $scoringQueryBuilder->inverseDocumentFrequency(new TermBuilder($field, $term));
                $result[$field][$term] = [
                    'idf' => $idf,
                    'count' => $parameter['count'],
                    'complete' => $idf * $parameter['count'],
                ];
            }
            uasort(
                $result[$field],
                function ($a, $b) {
                    return $a['idf'] <=> $b['idf'];
                }
            );
            $result[$field] = array_reverse($result[$field]);
        }

        return $result;
    }

    private function likeText(MoreLikeThisQuery $query, TextLike $like, array &$terms)
    {
        foreach ($query->getFields() as $field) {
            $this->analyzeText($field, $like->getText(), $terms);
        }
    }

    private function likeDocument(MoreLikeThisQuery $query, DocumentLike $like, array &$terms)
    {
        $index = $this->client->get($like->getIndex());
        $document = $index->get($like->getType(), $like->getId());

        foreach ($query->getFields() as $field) {
            $this->analyzeText($field, $document['_source'][$field], $terms);
        }
    }

    private function likeArtificialDocument(MoreLikeThisQuery $query, ArtificialDocumentLike $like, array &$terms)
    {
        foreach ($query->getFields() as $field) {
            $this->analyzeText($field, $like->getDocument()[$field], $terms);
        }
    }

    private function analyzeText(string $field, string $text, array &$terms)
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

    private function getMustNotQueries(array $likes)
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

        return [new IdsBuilder($ids)];
    }
}
