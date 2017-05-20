<?php

namespace Pucene\Component\Mapping;

/**
 * Mapping types.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-types.html
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

    // Binary types
    const BINARY = 'binary';

    /**
     * Get types.
     *
     * @return string[]
     */
    public static function getTypes()
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
            self::BINARY,
        ];
    }

    /**
     * Get string types.
     *
     * @return string[]
     */
    public static function getStringTypes()
    {
        return [
            self::TEXT,
            self::KEYWORD,
        ];
    }

    /**
     * Get numeric types.
     *
     * @return string[]
     */
    public static function getNumericTypes()
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
     * Get integer types.
     *
     * @return string[]
     */
    public static function getIntegerTypes()
    {
        return [
            self::LONG,
            self::INTEGER,
            self::SHORT,
            self::BYTE,
        ];
    }

    /**
     * Get float types.
     *
     * @return array
     */
    public static function getFloatTypes()
    {
        return [
            self::DOUBLE,
            self::FLOAT,
            self::HALF_FLOAT,
            self::SCALED_FLOAT,
        ];
    }
}
