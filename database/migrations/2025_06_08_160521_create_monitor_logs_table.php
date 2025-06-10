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
        Schema::create(
			'monitor_logs',
			function ( Blueprint $table ) {
				$table->id();
				$table->foreignId( 'site_id' )->constrained()->onDelete( 'cascade' );
				$table->string( 'url' );
				$table->string( 'uptime_status' );
				$table->text( 'uptime_check_failure_reason' )->nullable();
				$table->string( 'certificate_status' )->nullable();
				$table->string( 'certificate_issuer' )->nullable();
				$table->timestamp( 'certificate_expiration_date' )->nullable();
				$table->string( 'certificate_check_failure_reason' )->nullable();
				$table->string( 'uptime_check_method' )->default( 'get' );
				$table->text( 'uptime_check_payload' )->nullable();
				$table->text( 'uptime_check_additional_headers' )->nullable();
				$table->string( 'uptime_check_response_checker' )->nullable();
				$table->integer( 'response_time_ms' )->nullable();
				$table->integer( 'response_status_code' )->nullable();
				$table->text( 'response_body' )->nullable();
				$table->timestamps();
			}
		);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_logs');
    }
};
