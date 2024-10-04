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
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique();
            $table->string('title')->nullable();
            $table->json('vulnerabilities')->nullable();
            $table->longText('description')->nullable();
        });

        Schema::create('plugin_site', function (Blueprint $table) {
            $table->timestamps();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('plugin_id')->nullable()->constrained()->onDelete('cascade');
            $table->string( 'version' )->nullable();
            $table->string( 'update_version' )->nullable();
            $table->boolean('is_vulnerable')->default(false);
            $table->boolean('is_notified')->default(true);
            $table->json('vuln_ids')->nullable();
            $table->string( 'status' )->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugins');
    }
};
