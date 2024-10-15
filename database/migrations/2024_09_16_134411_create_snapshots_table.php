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
        Schema::create('snapshots', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('enabled')->default(1);
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->string('remote_path')->nullable();
            $table->string('local_path')->nullable();
            $table->foreignId('backup_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->bigInteger('size')->nullable();
            $table->date('deletion_date')->nullable(); 
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snapshots');
    }
};
