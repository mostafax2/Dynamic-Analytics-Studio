<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50);        // widget_viewed|report_run|dashboard_opened|export_done
            $table->string('resource_type', 30);     // widget|report|dashboard
            $table->unsignedBigInteger('resource_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->timestamp('occurred_at')->useCurrent();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['resource_type', 'resource_id']);
            $table->index(['user_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_analytics_events');
    }
};
