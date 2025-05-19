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
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->integer('ref_counter')->default(1);
            $table->json('settings')->default('{}');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('contact_person')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_template')->default(false);
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('invoices');
            $table->json('template_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'ref_counter', 'settings']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'contact_person']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn(['is_template', 'template_id', 'template_data']);
        });
    }
};
