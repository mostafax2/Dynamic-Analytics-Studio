<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_id')->constrained('as_dashboards')->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('config')->default('{}');
            $table->json('position')->default('{"x":0,"y":0,"w":4,"h":3}');
            $table->json('styling')->default('{}');
            $table->unsignedSmallInteger('refresh_interval')->default(300);
            $table->boolean('cache_enabled')->default(true);
            $table->unsignedSmallInteger('cache_ttl')->default(300);
            $table->unsignedBigInteger('report_id')->nullable();
            $table->json('report_params')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['dashboard_id', 'type']);
            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_widgets');
    }
};
