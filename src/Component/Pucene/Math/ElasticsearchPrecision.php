<?php

namespace Pucene\Component\Pucene\Math;

final class ElasticsearchPrecision
{
    /**
     * Calculates field-norm with precision of 3-bit mantissa.
     *
     * See lucene issue:
     * https://issues.apache.org/jira/browse/LUCENE-5005
     */
    public static function fieldNorm(int $length): float
    {
        if (1 === $length || 0 === $length) {
            return $length;
        }

        $fieldNorm = 1 / sqrt($length);
        $exponent = (floor(log($fieldNorm, 2)) + 1);
        $mantissa = ($fieldNorm * pow(2, -$exponent));

        $byte = [];
        for ($i = 0; $i < 3; ++$i) {
            $mantissa *= 2;
            $byte[] = $mantissa >= 1 ? 1 : 0;

            if ($mantissa >= 1) {
                $mantissa -= 1;
            }
        }

        $mantissa = 0;
        for ($i = 1; $i <= count($byte); ++$i) {
            $mantissa += (pow(2, (-1) * $i) * $byte[$i - 1]);
        }

        return $mantissa * pow(2, $exponent);
    }
}
