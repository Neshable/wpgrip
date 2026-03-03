<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();

            // e.g. plugin.updated, site.created, user.invited, backup.created, git.deployed
            $table->string('action', 80)->index();
            // category: site, plugin, theme, backup, git, user, system
            $table->string('category', 40)->default('system')->index();

            // Human-readable label of the thing acted on (survives deletion)
            $table->string('subject_label')->nullable();
            // Extra context (version numbers, branch names, etc.)
            $table->json('meta')->nullable();

            $table->string('ip_address', 45)->nullable();
            // outcome: success | failed | pending
            $table->string('status', 16)->default('success');

            // Logs are immutable - no updated_at
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
