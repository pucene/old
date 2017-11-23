<?php

namespace Pucene\Component\Pucene\Mapping;

use Pucene\Component\Mapping\Types;

class TypeGuesser
{
    /**
     * @param mixed $value
     */
    public function guess($value): string
    {
        if (is_bool($value)) {
            return Types::BOOLEAN;
        } elseif (is_int($value)) {
            return Types::INTEGER;
        } elseif (is_float($value)) {
            return Types::FLOAT;
        } elseif ($this->isDate($value)) {
            return Types::DATE;
        } elseif (is_string($value)) {
            return Types::TEXT;
        }

        throw new \Exception('Type could not be guessed');
    }

    private function isDate($value)
    {
        if ($value instanceof \DateTime) {
            return true;
        }

        return false !== strtotime($value);
    }
}
