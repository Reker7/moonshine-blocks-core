<?php

declare(strict_types=1);

namespace Reker7\MoonShineBlocksCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'sorting',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'sorting' => 'int',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
