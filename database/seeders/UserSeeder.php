<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Merchant Users (one for each merchant)
        $merchantAccounts = [
            [
                'merchant_id' => 1,
                'name' => 'Warung Padang Admin',
                'email' => 'warungpadang@example.com',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
            ],
            [
                'merchant_id' => 2,
                'name' => 'Sate Senayan Admin',
                'email' => 'satesenayan@example.com',
                'address' => 'Jl. Kebon Sirih No. 45, Jakarta Pusat',
                'latitude' => -6.1951,
                'longitude' => 106.8231,
            ],
            [
                'merchant_id' => 3,
                'name' => 'Bakso Malang Admin',
                'email' => 'baksomalang@example.com',
                'address' => 'Jl. Thamrin No. 67, Jakarta Pusat',
                'latitude' => -6.1944,
                'longitude' => 106.8229,
            ],
            [
                'merchant_id' => 4,
                'name' => 'Nasi Goreng Admin',
                'email' => 'nasigoreng@example.com',
                'address' => 'Jl. Kebon Sirih Raya No. 89, Jakarta Pusat',
                'latitude' => -6.1875,
                'longitude' => 106.8304,
            ],
            [
                'merchant_id' => 5,
                'name' => 'Ayam Geprek Admin',
                'email' => 'ayamgeprek@example.com',
                'address' => 'Jl. Sabang No. 12, Jakarta Pusat',
                'latitude' => -6.1867,
                'longitude' => 106.8286,
            ],
        ];

        foreach ($merchantAccounts as $account) {
            User::create([
                'merchant_id' => $account['merchant_id'],
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make('password'),
                'role' => 'merchant',
                'address' => $account['address'],
                'latitude' => $account['latitude'],
                'longitude' => $account['longitude'],
            ]);
        }

        // Create Customer Users
        $customerAccounts = [
            [
                'name' => 'Budi Santoso',
                'email' => 'customer1@example.com',
                'address' => 'Jl. Menteng Raya No. 10, Jakarta Pusat',
                'latitude' => -6.1950,
                'longitude' => 106.8300,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'customer2@example.com',
                'address' => 'Jl. Cikini Raya No. 25, Jakarta Pusat',
                'latitude' => -6.1920,
                'longitude' => 106.8350,
            ],
            [
                'name' => 'Ahmad Wijaya',
                'email' => 'customer3@example.com',
                'address' => 'Jl. Salemba Raya No. 15, Jakarta Pusat',
                'latitude' => -6.2000,
                'longitude' => 106.8400,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'customer4@example.com',
                'address' => 'Jl. Matraman Raya No. 30, Jakarta Timur',
                'latitude' => -6.2100,
                'longitude' => 106.8500,
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'customer5@example.com',
                'address' => 'Jl. Gatot Subroto No. 50, Jakarta Selatan',
                'latitude' => -6.2200,
                'longitude' => 106.8300,
            ],
        ];

        foreach ($customerAccounts as $account) {
            User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'address' => $account['address'],
                'latitude' => $account['latitude'],
                'longitude' => $account['longitude'],
            ]);
        }
    }
}
