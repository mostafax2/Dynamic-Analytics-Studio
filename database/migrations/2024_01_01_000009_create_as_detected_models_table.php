<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_detected_models', function (Blueprint $table) {
            $table->id();
            $table->string('class')->unique();
            $table->string('name');
            $table->string('table_name');
            $table->string('module')->nullable();
            $table->json('fillable')->default('[]');
            $table->json('casts')->default('{}');
            $table->json('relationships')->default('[]');
            $table->json('columns')->default('[]');
            $table->boolean('has_soft_deletes')->default(false);
            $table->string('primary_key')->default('id');
            $table->boolean('auto_generated_widgets')->default(false);
            $table->boolean('auto_generated_dashboard')->default(false);
            $table->timestamps();

            $table->index('module');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_detected_models');
    }
};
