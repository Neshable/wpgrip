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
            $table->string('name')->nullable();
            $table->enum('type', ['core', 'plugin'] )->nullable();
            $table->float('version')->nullable(); 
            $table->text('vulnerability_description')->nullable();   
            $table->date('reported_at')->nullable(); 
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
