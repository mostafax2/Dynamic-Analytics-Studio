<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_export_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);              // report|widget|dashboard
            $table->unsignedBigInteger('resource_id');
            $table->string('format', 10);
            $table->json('params')->nullable();
            $table->string('status', 20)->default('pending'); // pending|processing|done|failed
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->string('filename')->nullable();
            $table->unsignedInteger('rows')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->text('error')->nullable();
            $table->string('notify_email')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_by']);
            $table->index(['type', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_export_jobs');
    }
};
