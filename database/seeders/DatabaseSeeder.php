<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('orchid:admin admin admin@billseye.ch admin');

        $adminUser = User::firstWhere('name', 'admin');

        $s3dlAccount = Account::create([
            'email' => 'contact@3sdl.ch',
            'smtp_config' => [
                'host' => 'mail.infomaniak.com',
                'port' => '587',
                'password' => config('mail.mailers.smtp.password')
            ],
            'iban' => 'CH07 0900 0000 1257 3316 6',
            'name' => '3SDL',
            'street' => 'Rue Neuve',
            'building_number' => '3',
            'postal_code' => '2300',
            'city' => 'La Chaux-de-Fonds',
            'country' => 'CH',
        ]);

        $testUser = User::factory()->create([
            'name' => 'test',
            'email' => 'test@billseye.ch',
            'permissions' => ['platform.index' => true],
        ]);

        $testAccount = Account::factory()->create();

        $adminUser->accounts()->attach([
            $s3dlAccount->id => ['selected' => true],
            $testAccount->id => ['selected' => false],
        ]);

        $testUser->accounts()->attach($testAccount, ['selected' => true]);

        Customer::factory()->count(5)->create([
            'account_id' => $s3dlAccount->id,
        ]);

        Customer::factory()->create([
            'account_id' => $testAccount->id,
        ]);
    }
}
