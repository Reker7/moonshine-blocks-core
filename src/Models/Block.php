<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Reker7\MoonShineFieldsBuilder\Fields\FieldsBuilder\FieldsCollection;

class Block extends Model
{
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

    /**
     * Field presets attached to this block
     */
    public function fieldPresets(): BelongsToMany
    {
        return $this->belongsToMany(FieldPreset::class, 'block_field_preset')
            ->withPivot('sorting')
            ->withTimestamps()
            ->orderByPivot('sorting');
    }

    /**
     * Get all fields merged: presets + block custom fields
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMergedFields(): array
    {
        $fields = [];

        // Add fields from presets (in order)
        foreach ($this->fieldPresets as $preset) {
            $presetFields = $preset->fields;

            // Handle string JSON or null
            if (is_string($presetFields)) {
                $presetFields = json_decode($presetFields, true) ?: [];
            }
            if (!is_array($presetFields)) {
                $presetFields = [];
            }

            foreach ($presetFields as $field) {
                $fields[] = $field;
            }
        }

        // Add block's own custom fields
        $customFields = $this->fields;

        // Handle string JSON or null
        if (is_string($customFields)) {
            $customFields = json_decode($customFields, true) ?: [];
        }
        if (!is_array($customFields)) {
            $customFields = [];
        }

        foreach ($customFields as $field) {
            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Get merged fields as FieldsCollection
     */
    public function getMergedFieldsCollection(): FieldsCollection
    {
        return FieldsCollection::fromMixed($this->getMergedFields());
    }

    /**
     * Get fields grouped by preset (for fieldset wrapping)
     *
     * @return array<int, array{name: string|null, fields: array}>
     */
    public function getGroupedFields(): array
    {
        $groups = [];

        // Add fields from presets (each as a separate group)
        foreach ($this->fieldPresets as $preset) {
            $presetFields = $preset->fields;

            if (is_string($presetFields)) {
                $presetFields = json_decode($presetFields, true) ?: [];
            }
            if (!is_array($presetFields)) {
                $presetFields = [];
            }

            if (!empty($presetFields)) {
                $groups[] = [
                    'name' => $preset->name,
                    'fields' => $presetFields,
                ];
            }
        }

        // Add block's own custom fields (without group name)
        $customFields = $this->fields;

        if (is_string($customFields)) {
            $customFields = json_decode($customFields, true) ?: [];
        }
        if (!is_array($customFields)) {
            $customFields = [];
        }

        if (!empty($customFields)) {
            $groups[] = [
                'name' => null, // No fieldset wrapper for custom fields
                'fields' => $customFields,
            ];
        }

        return $groups;
    }

    public function scopeEffectiveActive($q)
    {
        return $q->where('is_active', true)
            ->where(function ($qq) {
                $qq->whereNull('block_group_id')
                    ->orWhereHas('blockGroup', fn ($g) => $g->where('is_active', true));
            });
    }

    public function scopeInGroupSlug($q, ?string $slug)
    {
        if (! $slug) {
            return $q;
        }
        return $q->whereHas('blockGroup', fn ($g) => $g->where('slug', $slug));
    }

    public function scopeWithoutGroup($q)
    {
        return $q->whereNull('block_group_id');
    }
}
