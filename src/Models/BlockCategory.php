<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlockCategory extends Model
{
    protected $fillable = [
        'block_id',
        'name',
        'slug',
        'is_active',
        'sorting',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'sorting' => 'int',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BlockItem::class);
    }
}
