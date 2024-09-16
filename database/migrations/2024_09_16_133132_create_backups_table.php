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
        Schema::create('backups', function (Blueprint $table) 
        { 
            // $table->string('file_path')->nullable();
            $table->id();
            $table->timestamps();
            $table->boolean('enabled')->default(1);
            $table->string('provider')->nullable();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('checksum')->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('excluded_tables')->nullable();
            $table->string('excluded_files')->nullable();
            $table->integer('frequency')->nullable(); 
            $table->integer('retention_days')->nullable();
            $table->date('last_backup')->nullable(); 
            $table->date('next_backup')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
