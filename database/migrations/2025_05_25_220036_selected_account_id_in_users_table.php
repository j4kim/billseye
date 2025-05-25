<?php

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Account::class, 'selected_account_id')->nullOnDelete()->nullable();
        });

        $pivot = DB::table('account_user')->where('selected', true)->get();
        foreach ($pivot as $au) {
            User::where('id', $au->user_id)->update(['selected_account_id' => $au->account_id]);
            echo "\nMark account $au->account_id as selected for user $au->user_id\n";
        }

        Schema::table('account_user', function (Blueprint $table) {
            $table->dropColumn('selected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_user', function (Blueprint $table) {
            $table->boolean('selected')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['selected_account_id']);
        });
    }
};
