<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Reker7\MoonShineBlocksCore\Models\BlockGroup;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BlockGroup::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug')
                ->unique();

            $table->boolean('is_multiple')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->boolean('is_api_enabled')
                ->default(true);

            $table->jsonb('fields')
                ->nullable();

            $table->integer('sorting')
                ->default(500);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
