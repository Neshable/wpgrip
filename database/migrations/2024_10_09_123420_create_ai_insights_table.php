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
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type')->nullable(); // overall
            $table->string('model')->nullable(); // for the model used
            $table->string('stats')->nullable(); 
            $table->string('ai_response')->nullable(); 
            $table->integer('reccomendations')->default(0); 
            $table->integer('medium_issues')->default(0); 
            $table->integer('urgent_issues')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
