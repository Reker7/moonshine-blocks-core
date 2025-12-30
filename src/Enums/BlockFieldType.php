<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Enums;

use Reker7\MoonShineFieldsBuilder\Enums\FieldType;

/**
 * BlockFieldType - aliases FieldType for backwards compatibility
 */
enum BlockFieldType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case NUMBER = 'number';
    case SWITCHER = 'switcher';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case PHONE = 'phone';
    case SELECT = 'select';
    case JSON = 'json';
    case IMAGE = 'image';
    case FILE = 'file';

    public function toString(): string
    {
        return FieldType::from($this->value)->label();
    }

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return FieldType::toArray();
    }
}
