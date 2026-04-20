<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('entity_table');
            $table->uuid('entity_id')->nullable();
            $table->enum('action', ['create', 'update', 'delete', 'soft_delete', 'restore', 'status_change']);
            $table->json('changes_json')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->uuid('correlation_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['entity_table', 'created_at'], 'audit_logs_org_entity_created_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
