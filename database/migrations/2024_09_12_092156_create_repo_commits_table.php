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
        Schema::create('repo_commits', function (Blueprint $table) {
            $table->id();
            $table->timestamps();      
            $table->foreignId('pivot_id')->nullable()->constrained('site_repositories')->onDelete('cascade');
            $table->foreignId('repository_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('committer')->nullable();
            $table->string('branch')->nullable();
            $table->string('commit')->nullable();
            $table->string('message')->nullable();
            $table->boolean('success')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repo_commits');
    }
};
