<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Reker7\MoonShineFieldsBuilder\Fields\FieldsBuilder\FieldsCollection;

class FieldPreset extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'fields',
        'sorting',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'sorting' => 'int',
        ];
    }

    /**
     * Get fields config as FieldsCollection
     */
    public function getFieldsCollection(): FieldsCollection
    {
        return FieldsCollection::fromMixed($this->fields);
    }

    /**
     * Blocks using this preset
     */
    public function blocks(): BelongsToMany
    {
        return $this->belongsToMany(Block::class, 'block_field_preset')
            ->withPivot('sorting')
            ->withTimestamps()
            ->orderByPivot('sorting');
    }
}
