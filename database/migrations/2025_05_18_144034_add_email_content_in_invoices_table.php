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
        Schema::table('invoices', function (Blueprint $table) {
            $table->datetime('pdf_generated_at')->nullable();
            $table->text('email_content')->nullable();
            $table->datetime('email_sent_at')->nullable();
            $table->string('email_subject')->nullable();
            $table->string('email_cc')->nullable();
            $table->string('email_bcc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'pdf_generated_at',
                    'email_content',
                    'email_sent_at',
                    'email_subject',
                    'email_cc',
                    'email_bcc',
                ]
            );
        });
    }
};
