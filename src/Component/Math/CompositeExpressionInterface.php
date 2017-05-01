<?php

namespace Pucene\Component\Math;

interface CompositeExpressionInterface extends ExpressionInterface
{
    public function add(ExpressionInterface $part): self;
}
