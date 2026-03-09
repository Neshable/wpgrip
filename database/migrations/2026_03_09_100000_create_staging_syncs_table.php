<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staging_syncs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('staging_site_id')->constrained('sites')->cascadeOnDelete();
            $table->uuid('tenant_id');
            $table->foreign('tenant_id')->references('uuid')->on('tenants')->cascadeOnDelete();
            $table->string('status')->default('pending')->index();
            $table->string('strategy')->nullable(); // 'local' or 'relay'
            $table->boolean('sync_db')->default(true);
            $table->boolean('sync_uploads')->default(true);
            $table->unsignedBigInteger('db_size_bytes')->nullable();
            $table->unsignedBigInteger('uploads_size_bytes')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staging_syncs');
    }
};
