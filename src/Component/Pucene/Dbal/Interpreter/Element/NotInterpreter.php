<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\Element\NotElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;
use Pucene\Component\Symfony\Pool\PoolInterface;

class NotInterpreter implements InterpreterInterface
{
    /**
     * @var PoolInterface
     */
    private $interpreterPool;

    public function __construct(PoolInterface $interpreterPool)
    {
        $this->interpreterPool = $interpreterPool;
    }

    /**
     * @param NotElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder, string $index)
    {
        $interpreter = $this->getInterpreter($element->getElement());

        return 'NOT (' . $interpreter->interpret($element->getElement(), $queryBuilder, $index) . ')';
    }

    /**
     * @param NotElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, string $index)
    {
        $math = new MathExpressionBuilder();
        $interpreter = $this->getInterpreter($element->getElement());

        return $math->multiply(
            $math->value($element->getBoost()),
            $interpreter->scoring($element->getElement(), $scoring, $index)
        );
    }

    private function getInterpreter(ElementInterface $element): InterpreterInterface
    {
        return $this->interpreterPool->get(get_class($element));
    }
}
