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
        Schema::create('vulnerabilities', function (Blueprint $table) {
            
            $table->id();
            $table->string('uuid')->unique();  // Unique identifier for the vulnerability
            $table->foreignId('plugin_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('slug')->nullable();
            $table->enum('type', ['core', 'theme', 'php', 'nginx', 'mysql', 'other', 'plugin'] )->nullable();
            $table->string('name')->nullable();          
            $table->text('description')->nullable();  // Description of the vulnerability
            $table->string('max_version')->nullable();
            $table->json('operator')->nullable();         // JSON to store min_version, max_version, min_operator, max_operator
            $table->json('impact')->nullable();  // JSON to store the impact details
            $table->json('source')->nullable();  // JSON to store the source information (CVE, WPScan, etc.)
            $table->string('updated')->nullable(); 
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vulnerabilities');
    }
};
