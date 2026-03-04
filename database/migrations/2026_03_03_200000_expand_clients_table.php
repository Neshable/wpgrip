<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('company')->nullable()->after('name');
            $table->string('website')->nullable()->after('company');
            $table->string('phone', 50)->nullable()->after('email');
            $table->string('billing_email')->nullable()->after('email');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('status', 20)->default('active')->after('notes');
            $table->string('source', 50)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('monthly_value', 10, 2)->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->json('tags')->nullable();
            $table->string('avatar_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'company',
                'website',
                'phone',
                'billing_email',
                'address',
                'city',
                'postal_code',
                'timezone',
                'status',
                'source',
                'currency',
                'monthly_value',
                'contract_start',
                'contract_end',
                'tags',
                'avatar_path',
            ]);
        });
    }
};
