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

        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(1);
            $table->string('name');
            $table->string('url')->unique();
            $table->string('rest_url')->nullable();
            $table->string( 'db_prefix', 25 )->nullable();
            $table->boolean('is_staging')->default(0);
            $table->boolean('is_currently_down')->default(0);
            $table->boolean('backup_enabled')->default(0);
            $table->string('ssh_user');
            $table->string('error_log_path')->nullable();
            // Provider for website board - Trello, Notion, other.
            $table->string('board_provider')->nullable();
            $table->string('board_url')->nullable();
            $table->float('php_ver')->nullable();
            $table->string('dir_path')->nullable();
            $table->float('dir_size')->nullable();
            $table->string('wp_ver')->nullable();
            $table->float('cli_ver')->nullable();
            $table->boolean('ssh_connection')->default(0);
            $table->string('db_schedule')->nullable();
            $table->string('files_schedule')->nullable();
            $table->boolean('uptime_monitor')->default(0);
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('server_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
           // $table->foreignIdFor( UptimeMonitor::class )->nullable()->constrained()->onDelete('set null');
            $table->date('deleted_at')->nullable(); // for soft-deletes
            $table->timestamp('last_sync')->nullable(); // for soft-deletes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
