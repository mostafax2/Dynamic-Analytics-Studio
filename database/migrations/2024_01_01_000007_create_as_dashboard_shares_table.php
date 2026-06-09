<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_dashboard_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_id')->constrained('as_dashboards')->cascadeOnDelete();
            $table->morphs('shareable');           // user | role | department
            $table->string('permission', 20)->default('view'); // view | edit | manage
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('granted_by')->constrained('users');
            $table->timestamps();

            $table->unique(['dashboard_id', 'shareable_type', 'shareable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_dashboard_shares');
    }
};
