<?php

use App\Models\Account;
use App\Models\Customer;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Account::class);
            $table->foreignIdFor(Customer::class)->nullable();
            $table->date('date');
            $table->string('subject');
            $table->string('currency')->nullable();
            $table->decimal('amount', total: 8, places: 2)->nullable();
            $table->decimal('discount', 3, 2)->nullable();
            $table->text('footer')->nullable();
            $table->string('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
