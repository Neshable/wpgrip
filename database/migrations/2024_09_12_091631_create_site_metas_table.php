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
        Schema::create('sites_meta', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('cascade');
            $table->longtext('db_tables')->nullable();
            $table->integer('db_size')->nullable();
            $table->float('core_version')->nullable();
            $table->boolean('is_vulnerable')->default(false);
            $table->text('themes')->nullable();
            $table->string('active_theme')->nullable();
            $table->string('batch_id', 99)->nullable();
            // $table->string('home_shot')->nullable();
            // $table->string('home_shot_b')->nullable();
            $table->boolean('has_issues')->default(false);
            $table->text('issues')->nullable();
            $table->timestamp('lighthouse_last_sync')->nullable();
            $table->text('lighthouse_mobile', 150)->nullable();
            $table->text('lighthouse_desktop', 150)->nullable();
            // $table->integer('lighthouse_result')->nullable();
            // $table->text('lighthouse_meta')->nullable();
            $table->text('blacklisted_info')->nullable();
            $table->date('deleted_at')->nullable(); // for soft-deletes          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_metas');
    }
};
