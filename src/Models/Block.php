<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Reker7\MoonShineFieldsBuilder\Fields\FieldsBuilder\FieldsCollection;

class Block extends Model
{
    use SoftDeletes;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'slug',
        'name',
        'is_active',
        'is_multiple',
        'is_api_enabled',
        'sorting',
        'fields',
        'block_group_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_multiple' => 'boolean',
            'is_api_enabled' => 'boolean',
            'fields' => 'array',
        ];
    }

    /**
     * Get fields config as FieldsCollection
     */
    public function getFieldsCollection(): FieldsCollection
    {
        return FieldsCollection::fromMixed($this->fields);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BlockItem::class)
            ->orderBy('sorting');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(BlockCategory::class);
    }

    public function blockGroup(): BelongsTo
    {
        return $this->belongsTo(BlockGroup::class);
    }

    public function scopeEffectiveActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(function (Builder $qq): void {
                $qq->whereNull('block_group_id')
                    ->orWhereHas('blockGroup', fn (Builder $g) => $g->where('is_active', true));
            });
    }

    public function scopeInGroupSlug(Builder $q, ?string $slug): Builder
    {
        if (! $slug) {
            return $q;
        }
        return $q->whereHas('blockGroup', fn (Builder $g) => $g->where('slug', $slug));
    }

    public function scopeWithoutGroup(Builder $q): Builder
    {
        return $q->whereNull('block_group_id');
    }
}
