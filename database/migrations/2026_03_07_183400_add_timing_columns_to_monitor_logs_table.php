<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitor_logs', function (Blueprint $table) {
            $table->float('dns_time_ms')->nullable()->after('response_time_ms')->comment('DNS lookup time in ms');
            $table->float('connect_time_ms')->nullable()->after('dns_time_ms')->comment('TCP connect time in ms');
            $table->float('tls_time_ms')->nullable()->after('connect_time_ms')->comment('TLS handshake time in ms');
            $table->float('ttfb_ms')->nullable()->after('tls_time_ms')->comment('Time to first byte in ms');
            $table->float('transfer_time_ms')->nullable()->after('ttfb_ms')->comment('Content transfer time in ms');

            $table->index(['site_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('monitor_logs', function (Blueprint $table) {
            $table->dropIndex(['site_id', 'created_at']);
            $table->dropColumn(['dns_time_ms', 'connect_time_ms', 'tls_time_ms', 'ttfb_ms', 'transfer_time_ms']);
        });
    }
};
