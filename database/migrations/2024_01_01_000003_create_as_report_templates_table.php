<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('data_source');           // table name or collection
            $table->string('source_type', 20)->default('mysql');  // mysql | mongodb
            $table->json('columns')->default('[]');
            $table->json('filters')->default('[]');
            $table->json('group_by')->default('[]');
            $table->json('order_by')->default('[]');
            $table->json('aggregations')->default('[]');
            $table->json('joins')->default('[]');
            $table->json('settings')->default('{}');
            $table->boolean('is_template')->default(false);
            $table->string('category')->nullable()->index();
            $table->json('tags')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['created_by', 'is_template']);
            $table->index('source_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_report_templates');
    }
};
