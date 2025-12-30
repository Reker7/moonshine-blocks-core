<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Reker7\MoonShineBlocksCore\Models\Block;
use Reker7\MoonShineBlocksCore\Models\FieldPreset;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('block_field_preset', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Block::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(FieldPreset::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('sorting')->default(500);
            $table->timestamps();

            $table->unique(['block_id', 'field_preset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_field_preset');
    }
};
