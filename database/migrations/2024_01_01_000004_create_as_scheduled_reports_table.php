<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->string('name');
            $table->string('frequency', 20);          // daily|weekly|monthly|quarterly|yearly
            $table->string('cron_expression', 100);
            $table->string('format', 10)->default('pdf');
            $table->json('delivery_methods')->default('["email"]');
            $table->json('recipients')->default('[]');
            $table->string('webhook_url')->nullable();
            $table->json('params')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable()->index();
            $table->unsignedSmallInteger('failure_count')->default(0);
            $table->text('last_error')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->timestamps();

            $table->index(['is_active', 'next_run_at']);
            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_scheduled_reports');
    }
};
