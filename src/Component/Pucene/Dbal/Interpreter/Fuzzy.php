<?php

namespace Pucene\Component\Pucene\Dbal\Interpreter;

class Fuzzy
{
    const MODE_1 = 1;
    const MODE_2 = 2;
    const MODE_AUTO = 'auto';

    /**
     * Returns distance for given mode.
     *
     * @param mixed $mode
     *
     * @return int
     */
    public static function getFuzzyDistance(string $term, $mode): int
    {
        if (self::MODE_1 === $mode) {
            return 1;
        }

        if (self::MODE_2 === $mode) {
            return 2;
        }

        if (strlen($term) < 3) {
            return 1;
        }

        return 2;
    }

    /**
     * Returns fuzzy terms.
     *
     * @param mixed $mode 1, 2, 'AUTO'
     */
    public static function getFuzzyTerms(string $term, $mode): array
    {
        $distance = self::getFuzzyDistance($term, $mode);
        if (1 === $distance) {
            return self::generateFuzzyTerms($term);
        }

        return self::generateFuzzyTermsTwice($term);
    }

    /**
     * Generates fuzzy terms twice.
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
