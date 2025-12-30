<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Reker7\MoonShineBlocksCore\Models\Block;
use Reker7\MoonShineBlocksCore\Models\BlockCategory;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('block_items', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Block::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(BlockCategory::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title')
                ->nullable();
            $table->string('slug')
                ->nullable();
            $table->boolean('is_active')
                ->default(true);

            $table->json('data')
                ->nullable();

            $table->integer('sorting')
                ->default(500);

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['block_id', 'slug']);
            $table->index(['block_id', 'sorting']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_items');
    }
};
