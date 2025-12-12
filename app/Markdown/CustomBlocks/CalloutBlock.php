<?php

namespace App\Markdown\CustomBlocks;

/**
 * Callout Block for warnings, tips, notes, success, error messages
 *
 * Usage:
 * :::warning
 * **Title**
 * Content here
 * :::
 */
class CalloutBlock extends AbstractCustomBlock
{
    public const TYPE_WARNING = 'warning';
    public const TYPE_TIP = 'tip';
    public const TYPE_NOTE = 'note';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_ERROR = 'error';

    public const VALID_TYPES = [
        self::TYPE_WARNING,
        self::TYPE_TIP,
        self::TYPE_NOTE,
        self::TYPE_SUCCESS,
        self::TYPE_ERROR,
    ];

    public static function isCalloutType(string $type): bool
    {
        return in_array($type, self::VALID_TYPES, true);
    }
}
