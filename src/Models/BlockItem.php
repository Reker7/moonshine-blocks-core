<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'block_id',
        'block_category_id',
        'title',
        'slug',
        'is_active',
        'data',
        'published_at',
        'sorting',
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'bool',
        'sorting' => 'int',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlockCategory::class, 'block_category_id');
    }
}
