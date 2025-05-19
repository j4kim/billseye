<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Customer;
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

        Account::create([
            'email' => 'contact@3sdl.ch',
            'smtp_config' => [
                'host' => 'mail.infomaniak.com',
                'port' => '587',
                'username' => 'contact@3sdl.ch',
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

        Customer::factory()->count(10)->create();
    }
}
