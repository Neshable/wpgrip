<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) 
        {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('ip', 15)->unique();
            $table->string('private_ip', 15)->nullable();
            $table->string('ssh_port', 4)->nullable();
            $table->string('country')->nullable();
            $table->string('provider')->nullable();
            $table->string('type')->nullable();
            $table->float('ram_total')->nullable();
            $table->integer('cpu_cores')->nullable();
            // $table->float('cpu_load')->nullable();
            $table->float('hdd_total')->nullable();
            $table->float('hdd_free')->nullable();
            // $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->date('deleted_at')->nullable(); // for soft-deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
