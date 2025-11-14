<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchants = [
            [
                'name' => 'Warung Padang Sederhana',
                'description' => 'Authentic Padang cuisine with spicy and flavorful dishes',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'phone' => '021-12345678',
                'is_active' => true,
                'opening_time' => '08:00',
                'closing_time' => '22:00',
            ],
            [
                'name' => 'Sate Khas Senayan',
                'description' => 'Traditional Indonesian satay restaurant',
                'address' => 'Jl. Kebon Sirih No. 45, Jakarta Pusat',
                'latitude' => -6.1951,
                'longitude' => 106.8231,
                'phone' => '021-23456789',
                'is_active' => true,
                'opening_time' => '10:00',
                'closing_time' => '23:00',
            ],
            [
                'name' => 'Bakso Malang Cak Eko',
                'description' => 'Famous meatball soup from Malang',
                'address' => 'Jl. Thamrin No. 67, Jakarta Pusat',
                'latitude' => -6.1944,
                'longitude' => 106.8229,
                'phone' => '021-34567890',
                'is_active' => true,
                'opening_time' => '09:00',
                'closing_time' => '21:00',
            ],
            [
                'name' => 'Nasi Goreng Kambing Kebon Sirih',
                'description' => 'Special lamb fried rice restaurant',
                'address' => 'Jl. Kebon Sirih Raya No. 89, Jakarta Pusat',
                'latitude' => -6.1875,
                'longitude' => 106.8304,
                'phone' => '021-45678901',
                'is_active' => true,
                'opening_time' => '17:00',
                'closing_time' => '02:00',
            ],
            [
                'name' => 'Ayam Geprek Bensu',
                'description' => 'Crispy smashed fried chicken',
                'address' => 'Jl. Sabang No. 12, Jakarta Pusat',
                'latitude' => -6.1867,
                'longitude' => 106.8286,
                'phone' => '021-56789012',
                'is_active' => true,
                'opening_time' => '10:00',
                'closing_time' => '22:00',
            ],
        ];

        foreach ($merchants as $merchant) {
            Merchant::create($merchant);
        }
    }
}
