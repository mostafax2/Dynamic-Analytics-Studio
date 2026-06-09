<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_widget_types', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->unique();
            $table->string('label');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('handler_class');
            $table->json('default_config')->default('{}');
            $table->json('config_schema')->default('{}');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_widget_types');
    }
};
