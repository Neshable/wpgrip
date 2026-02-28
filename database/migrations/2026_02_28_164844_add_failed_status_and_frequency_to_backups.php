<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // backups.frequency was integer but code uses string values (daily/weekly/etc)
        // Change to string. Also add db_size, file_path, status, delete_date columns
        // that RemoteDBBackup tried to write but were missing from the original migration.
        Schema::table('backups', function (Blueprint $table) {
            // Only add if they don't already exist
            if (!Schema::hasColumn('backups', 'status')) {
                $table->string('status')->nullable()->default('active');
            }
            if (!Schema::hasColumn('backups', 'file_path')) {
                $table->string('file_path')->nullable();
            }
            if (!Schema::hasColumn('backups', 'db_size')) {
                $table->bigInteger('db_size')->nullable();
            }
            if (!Schema::hasColumn('backups', 'delete_date')) {
                $table->date('delete_date')->nullable();
            }
        });

        // Snapshots: ensure deletion_date is datetime not date (backups can be
        // created multiple times per day; date precision is too coarse)
        // Nothing to change structurally, just documenting.
    }

    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('backups', 'status')    ? 'status'    : null,
                Schema::hasColumn('backups', 'file_path') ? 'file_path' : null,
                Schema::hasColumn('backups', 'db_size')   ? 'db_size'   : null,
                Schema::hasColumn('backups', 'delete_date') ? 'delete_date' : null,
            ]));
        });
    }
};
