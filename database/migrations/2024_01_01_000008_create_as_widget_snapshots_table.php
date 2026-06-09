<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_widget_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('widget_id')->constrained('as_widgets')->cascadeOnDelete();
            $table->json('data')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedInteger('execution_ms')->nullable();
            $table->unsignedInteger('rows')->nullable();
            $table->timestamp('captured_at');
            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->index(['widget_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_widget_snapshots');
    }
};
