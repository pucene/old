<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter;

class Fuzzy
{
    const MODE_1 = 1;
    const MODE_2 = 2;
    const MODE_AUTO = 'auto';

    /**
     * Returns fuzzy terms.
     *
     * @param string $term
     * @param mixed $mode 1, 2, 'AUTO'
     *
     * @return array
     */
    public static function getFuzzyTerms(string $term, $mode): array
    {
        if ($mode === self::MODE_1) {
            return self::generateFuzzyTerms($term);
        }

        if ($mode === self::MODE_1) {
            return self::generateFuzzyTermsTwice($term);
        }

        if (strlen($term) < 3) {
            return [$term];
        } elseif (strlen($term) < 5) {
            return self::generateFuzzyTerms($term);
        }

        return self::generateFuzzyTermsTwice($term);
    }

    /**
     * Generates fuzzy terms twice.
     *
     * @param string $term
     *
     * @return array
     */
    private static function generateFuzzyTermsTwice(string $term): array
    {
        $terms = [];
        foreach (self::generateFuzzyTerms($term) as $term) {
            $terms[] = $term;
            $terms = array_merge($terms, self::generateFuzzyTerms($term));
        }

        return $terms;
    }

    /**
     * Generates fuzzy terms.
     *
     * @param string $term
     *
     * @return array
     */
    private static function generateFuzzyTerms(string $term): array
    {
        $terms = [$term . '_'];
        for ($i = 0; $i < strlen($term); ++$i) {
            // insertions
            $terms[] = substr($term, 0, $i) . '_' . substr($term, $i);
            // deletions
            $terms[] = substr($term, 0, $i) . substr($term, $i + 1);
            // substitutions
            $terms[] = substr($term, 0, $i) . '_' . substr($term, $i + 1);
        }

        return $terms;
    }
}
