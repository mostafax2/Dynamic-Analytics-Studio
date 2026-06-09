<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_dashboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('layout')->default('{}');
            $table->json('settings')->default('{}');
            $table->boolean('is_public')->default(false);
            $table->string('public_token', 64)->nullable()->unique();
            $table->timestamp('public_expires_at')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('permissions')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['created_by', 'tenant_id']);
            $table->index(['is_public', 'public_token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_dashboards');
    }
};
