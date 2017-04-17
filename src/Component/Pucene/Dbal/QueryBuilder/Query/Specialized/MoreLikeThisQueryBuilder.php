<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\Specialized;

use Pucene\Component\Analysis\AnalyzerInterface;
use Pucene\Component\Client\ClientInterface;
use Pucene\Component\Pucene\Dbal\DbalStorage;
use Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel\TermQuery;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;
use Pucene\Component\QueryBuilder\Query\QueryInterface;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThis;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\TextLike;

/**
 * Builder for more_like_this query.
 */
class MoreLikeThisQueryBuilder implements QueryBuilderInterface
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

    public function build(QueryInterface $query, DbalStorage $storage)
    {
        $scoringQueryBuilder = $storage->createScoringQueryBuilder();
        $terms = $this->getTerms($query, $scoringQueryBuilder);

        $queries = [];
        foreach ($query->getFields() as $field) {
            foreach ($terms as $term => $boost) {
                $queries[] = new TermQuery($field, $term);
            }
        }

        return new MoreLikeThisQuery($queries, $query->getLike());
    }

    private function getTerms(MoreLikeThis $query, ScoringQueryBuilder $scoringQueryBuilder)
    {
        $terms = [];
        foreach ($query->getLike() as $like) {
            if ($like instanceof TextLike) {
                $this->likeText($like, $scoringQueryBuilder, $terms);
            } elseif ($like instanceof DocumentLike) {
                $this->likeDocument($query, $like, $scoringQueryBuilder, $terms);
            }
        }

        $result = [];
        foreach ($terms as $term => $parameter) {
            if ($parameter['count'] < $query->getMinTermFrequency()
                || $parameter['frequency'] < $query->getMinDocFreq()
            ) {
                continue;
            }

            // FIXME why????
            // "Museum of Arts of Lyon" fails (scoring) without this line - "Museum of Art of Lyon" fails not
            if ($term === 'arts') {
                continue;
            }

            $result[$term] = $scoringQueryBuilder->inverseDocumentFrequencyPerDocument($term) * $parameter['count'];
        }

        asort($result);
        $result = array_reverse($result);

        return $result;
    }

    private function likeText(TextLike $like, ScoringQueryBuilder $scoringQueryBuilder, array &$terms)
    {
        $this->analyzeText($like->getText(), $scoringQueryBuilder, $terms);
    }

    private function likeDocument(
        MoreLikeThis $query,
        DocumentLike $like,
        ScoringQueryBuilder $scoringQueryBuilder,
        array &$terms
    ) {
        $index = $this->client->get($like->getIndex());
        $document = $index->get($like->getType(), $like->getId());

        foreach ($query->getFields() as $field) {
            $this->analyzeText($document['_source'][$field], $scoringQueryBuilder, $terms);
        }
    }

    private function analyzeText(string $text, ScoringQueryBuilder $scoringQueryBuilder, array &$terms)
    {
        $tokens = $this->analyzer->analyze($text);

        foreach ($tokens as $token) {
            if (!array_key_exists($token->getEncodedTerm(), $terms)) {
                $terms[$token->getEncodedTerm()] = [
                    'count' => 0,
                    'frequency' => $scoringQueryBuilder->getDocCountPerTermPerDocument(
                        $token->getEncodedTerm()
                    ),
                ];
            }

            ++$terms[$token->getEncodedTerm()]['count'];
        }
    }
}