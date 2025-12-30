<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Reker7\MoonShineBlocksCore\Models\Block;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('block_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Block::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->integer('sorting')->default(500);

            $table->softDeletes();
            $table->timestamps();


            $table->unique(['block_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_categories');
    }
};
