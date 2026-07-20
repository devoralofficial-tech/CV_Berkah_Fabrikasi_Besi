<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@cvberkah.id'],
            [
                'name'     => 'Admin CV Berkah',
                'email'    => 'admin@cvberkah.id',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );


        // Company settings (single row)
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'wa_number' => '628123456789',
                'address' => 'Jl. Industri No. 1, Kota Anda',
                'email' => 'info@cvberkah.id',
                'company_description' => 'CV Berkah adalah penyedia material besi dan logam berkualitas untuk kebutuhan konstruksi, industri, dan bengkel. Berpengalaman lebih dari 10 tahun melayani pelanggan se-Indonesia.',
                'operating_hours' => 'Senin–Sabtu, 08.00–17.00 WIB'
            ]
        );

        // Categories
        $categoryData = [
            'Besi Baja' => [
                'Besi Siku', 'Besi UNP (Kanal U)', 'Besi INP (Kanal H)', 'Besi Beton Ulir', 'Besi Beton Polos',
                'Besi Hollow', 'Besi Hollow Kotak', 'Besi WF (Wide Flange)',
            ],
            'Pipa' => [
                'Pipa Besi Hitam', 'Pipa Galvanis', 'Pipa Stainless', 'Pipa Square (Kotak)', 'Pipa Round',
            ],
            'Plat Baja' => [
                'Plat Hitam', 'Plat Galvanis', 'Plat Bordes (Bergerigi)', 'Plat Strip',
            ],
            'Stainless Steel' => [
                'Pipa Stainless 201', 'Pipa Stainless 304', 'Plat Stainless', 'Batang Stainless (Rod)',
            ],
            'Atap & Dinding' => [
                'Atap Spandek', 'Atap Bondek', 'Genteng Metal',
            ],
        ];

        foreach ($categoryData as $parentName => $children) {
            $parent = Category::updateOrCreate(
                ['name' => $parentName, 'parent_id' => null],
                ['name' => $parentName, 'slug' => \Illuminate\Support\Str::slug($parentName), 'parent_id' => null]
            );
            foreach ($children as $childName) {
                Category::updateOrCreate(
                    ['name' => $childName, 'parent_id' => $parent->id],
                    ['name' => $childName, 'slug' => \Illuminate\Support\Str::slug($childName), 'parent_id' => $parent->id]
                );
            }
        }
    }
}
