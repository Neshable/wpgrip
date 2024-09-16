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

        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('provider')->nullable();
            $table->string('webhook')->nullable();
            $table->string('description')->nullable();
            $table->string('secret')->nullable();
            $table->string('remote')->nullable();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_pull')->nullable(); 
        });

        Schema::create('site_repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('repository_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_active')->nullable();
            $table->boolean('auto_deploy')->nullable();
            $table->string('status')->nullable();
            $table->string('path');
            $table->string('branch');
            $table->timestamp('last_pull')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};
