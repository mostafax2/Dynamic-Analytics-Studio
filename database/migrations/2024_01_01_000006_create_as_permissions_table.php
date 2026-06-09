<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('as_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name', 30)->default('web');
            $table->string('group', 50)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('as_role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['role_id', 'permission_id']);
            $table->index('permission_id');
        });

        Schema::create('as_user_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->boolean('granted')->default(true);
            $table->primary(['user_id', 'permission_id']);
        });

        Schema::create('as_rls_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->string('column');
            $table->string('scope', 30);   // branch|department|tenant|user|custom
            $table->string('operator', 20)->default('=');
            $table->string('value_source', 30)->default('auth_user'); // auth_user|config|static
            $table->string('value_key')->nullable();
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->timestamps();

            $table->index(['model', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('as_rls_policies');
        Schema::dropIfExists('as_user_permissions');
        Schema::dropIfExists('as_role_permissions');
        Schema::dropIfExists('as_permissions');
    }
};
