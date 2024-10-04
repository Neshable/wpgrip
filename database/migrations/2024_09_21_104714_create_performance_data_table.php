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
        Schema::create('performance_data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('strategy');
            $table->integer('performance')->nullable();
            $table->bigInteger('fcp')->nullable();
            $table->bigInteger('total_blocking_time')->nullable();
            $table->bigInteger('speed_index')->nullable();
            $table->bigInteger('lcp')->nullable();
            $table->bigInteger('time_interactive')->nullable(); // millisecond
            $table->bigInteger('fmp')->nullable();
            $table->bigInteger('dom_size')->nullable();
            $table->bigInteger('network_server_latency')->nullable();
            $table->bigInteger('server_response_time')->nullable();            
        });

     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_data');
    }
};
