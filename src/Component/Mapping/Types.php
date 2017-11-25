<?php

namespace Pucene\Component\Mapping;

/**
 * Mapping types.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html
 */
class Types
{
    // String Types
    const TEXT = 'text';
    const KEYWORD = 'keyword';

    // Numeric Types
    const LONG = 'long';
    const INTEGER = 'integer';
    const SHORT = 'short';
    const BYTE = 'byte';
    const DOUBLE = 'double';
    const FLOAT = 'float';
    const HALF_FLOAT = 'half_float';
    const SCALED_FLOAT = 'scaled_float';

    // Date types
    const DATE = 'date';

    // Boolean types
    const BOOLEAN = 'boolean';

    /**
     * @return string[]
     */
    public static function getTypes(): array
    {
        return [
            self::TEXT,
            self::KEYWORD,
            self::LONG,
            self::INTEGER,
            self::SHORT,
            self::BYTE,
            self::DOUBLE,
            self::FLOAT,
            self::HALF_FLOAT,
            self::SCALED_FLOAT,
            self::DATE,
            self::BOOLEAN,
        ];
    }

    /**
     * @return string[]
     */
    public static function getStringTypes(): array
    {
        return [
            self::TEXT,
            self::KEYWORD,
        ];
    }

    /**
     * @return string[]
     */
    public static function getNumericTypes(): array
    {
        return [
            self::LONG,
            self::INTEGER,
            self::SHORT,
            self::BYTE,
            self::DOUBLE,
            self::FLOAT,
            self::HALF_FLOAT,
            self::SCALED_FLOAT,
        ];
    }

    /**
     * @return string[]
     */
    public static function getIntegerTypes(): array
    {
        return [
            self::LONG,
            self::INTEGER,
            self::SHORT,
            self::BYTE,
        ];
    }

    /**
     * @return array
     */
    public static function getFloatTypes(): array
    {
        return [
            self::DOUBLE,
            self::FLOAT,
            self::HALF_FLOAT,
            self::SCALED_FLOAT,
        ];
    }
}
