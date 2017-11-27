<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter\Element;

use Pucene\Component\Math\MathExpressionBuilder;
use Pucene\Component\Pucene\Compiler\Element\BoolElement;
use Pucene\Component\Pucene\Compiler\Element\CompositeElement;
use Pucene\Component\Pucene\Compiler\Element\TermElement;
use Pucene\Component\Pucene\Compiler\ElementInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\InterpreterInterface;
use Pucene\Component\Pucene\Dbal\Interpreter\PuceneQueryBuilder;
use Pucene\Component\Pucene\Dbal\ScoringAlgorithm;
use Pucene\Component\Symfony\Pool\PoolInterface;

class BoolInterpreter implements InterpreterInterface
{
    /**
     * @var PoolInterface
     */
    protected $interpreterPool;

    public function __construct(PoolInterface $interpreterPool)
    {
        $this->interpreterPool = $interpreterPool;
    }

    /**
     * @param BoolElement $element
     */
    public function interpret(ElementInterface $element, PuceneQueryBuilder $queryBuilder, string $index)
    {
        return $this->getInterpreter($element->getElement())->interpret($element->getElement(), $queryBuilder, $index);
    }

    /**
     * @param BoolElement $element
     */
    public function scoring(ElementInterface $element, ScoringAlgorithm $scoring, string $index)
    {
        if (0 === count($element->getScoringElements())) {
            return 0;
        } elseif (1 === count($element->getScoringElements())) {
            $innerElement = $element->getScoringElements()[0];
            /** @var InterpreterInterface $interpreter */
            $interpreter = $this->interpreterPool->get(get_class($innerElement));

            return $interpreter->scoring($innerElement, $scoring, $index);
        }

        $math = new MathExpressionBuilder();

        $expression = $math->add();
        foreach ($element->getScoringElements() as $innerElement) {
            /** @var InterpreterInterface $interpreter */
            $interpreter = $this->interpreterPool->get(get_class($innerElement));

            $expression->add($interpreter->scoring($innerElement, $scoring, $index));
        }

        return $expression;
    }

    private function getTerms(array $elements)
    {
        $terms = [];
        foreach ($elements as $innerElement) {
            if ($innerElement instanceof TermElement) {
                $terms[] = $innerElement;
            } elseif ($innerElement instanceof CompositeElement) {
                $terms = array_merge($terms, $this->getTerms($innerElement->getElements()));
            }
        }

        return $terms;
    }

    private function getInterpreter(ElementInterface $element): InterpreterInterface
    {
        return $this->interpreterPool->get(get_class($element));
    }
}
