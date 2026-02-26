<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blocks', function (Blueprint $table): void {
            $table->boolean('has_categories')->default(false)->after('is_multiple');
        });
    }

    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table): void {
            $table->dropColumn('has_categories');
        });
    }
};
