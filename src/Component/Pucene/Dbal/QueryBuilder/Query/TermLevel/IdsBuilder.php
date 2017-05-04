<?php

namespace Pucene\Component\Pucene\Dbal\QueryBuilder\Query\TermLevel;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Dbal\QueryBuilder\QueryBuilderInterface;
use Pucene\Component\Pucene\Dbal\QueryBuilder\ScoringQueryBuilder;

class IdsBuilder implements QueryBuilderInterface
{
    /**
     * @var string[]
     */
    private $values;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string[] $values
     * @param string $type
     */
    public function __construct(array $values, string $type = null)
    {
        $this->values = $values;
        $this->type = $type;
    }

    public function build(ExpressionBuilder $expr, QueryBuilder $queryBuilder)
    {
        $expression = $expr->in(
            'document.id',
            array_map(
                function ($item) {
                    return "'" . $item . "'";
                },
                $this->values
            )
        );

        if (!$this->type) {
            return $expression;
        }

        return $expr->andX($expression, $expr->eq('document.type', "'" . $this->type . "'"));
    }

    public function scoring(MathExpressionBuilder $expr, ScoringQueryBuilder $queryBuilder)
    {
        return 1;
    }

    public function getTerms()
    {
        return [];
    }
}
