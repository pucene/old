<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Pucene\Compiler\Element\PrefixElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;
use Pucene\Component\Pucene\Mapping\Mapping;

class PrefixInterpreter implements InterpreterInterface
{
    /**
     * @var Mapping
     */
    private $mapping;

    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param PrefixElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder, string $index)
    {
        $expr = $queryBuilder->expr();

        $type = $this->mapping->getTypeForField($index, $element->getField());
        $alias = $queryBuilder->joinValue($element->getField(), $type);

        $prefix = mb_strtolower($element->getPrefix());
        $field = sprintf('LOWER(%s.value) COLLATE utf8_bin', $alias);

        return $expr->orX(
            $expr->like($field, sprintf("'%s%%'", $prefix)),
            $expr->like($field, sprintf("'%% %s%%'", $prefix))
        );
    }

    /**
     * @param PrefixElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, string $index)
    {
        return $scoring->getQueryBuilder()->math()->value($element->getBoost());
    }
}
