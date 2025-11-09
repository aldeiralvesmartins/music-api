<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = DB::table('users')->first()?->id ?? Str::uuid()->toString();

        $configs = [
            [
                'id' => Str::uuid(),
                'user_id' => $userId,
                'store_name' => 'Loja Solar',
                'primary_color' => '#F97316',
                'secondary_color' => '#FDBA74',
                'background_color' => '#FFF7ED',
                'text_color' => '#1F2937',
                'font_family' => 'Inter, sans-serif',
                'logo_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTVfvySz1H6jlEwkEW7fXvAVrTLYUnjts_RUg&s',
                'favicon_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTVfvySz1H6jlEwkEW7fXvAVrTLYUnjts_RUg&s',
                'custom_css' => null,
                'custom_js' => null,
                'is_active' => true, // Apenas esta ativa
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $userId,
                'store_name' => 'Eco Shop',
                'primary_color' => '#16A34A',
                'secondary_color' => '#BBF7D0',
                'background_color' => '#F0FDF4',
                'text_color' => '#064E3B',
                'font_family' => 'Poppins, sans-serif',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/2a/Eco-Shop_logo_with_colour.png',
                'favicon_url' => 'https://upload.wikimedia.org/wikipedia/commons/2/2a/Eco-Shop_logo_with_colour.png',
                'custom_css' => null,
                'custom_js' => null,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $userId,
                'store_name' => 'Fashion Luxe',
                'primary_color' => '#8B5CF6',
                'secondary_color' => '#DDD6FE',
                'background_color' => '#FAF5FF',
                'text_color' => '#312E81',
                'font_family' => 'Roboto, sans-serif',
                'logo_url' => 'https://play-lh.googleusercontent.com/mtw2to13etsVsuqV0bBpFnqvJ4QoIK5oFzNM0vAFlEVSxYvYl9IeeUT3XeD7yDMUlv4',
                'favicon_url' => 'https://play-lh.googleusercontent.com/mtw2to13etsVsuqV0bBpFnqvJ4QoIK5oFzNM0vAFlEVSxYvYl9IeeUT3XeD7yDMUlv4',
                'custom_css' => null,
                'custom_js' => null,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $userId,
                'store_name' => 'Tech World',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#93C5FD',
                'background_color' => '#EFF6FF',
                'text_color' => '#1E3A8A',
                'font_family' => 'Open Sans, sans-serif',
                'logo_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTymSMtrBAbXMCGO9ejrtD32NZG7TgkFBt3hw&s',
                'favicon_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTymSMtrBAbXMCGO9ejrtD32NZG7TgkFBt3hw&s',
                'custom_css' => null,
                'custom_js' => null,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $userId,
                'store_name' => 'Minimal Studio',
                'primary_color' => '#111827',
                'secondary_color' => '#9CA3AF',
                'background_color' => '#F9FAFB',
                'text_color' => '#111827',
                'font_family' => 'Nunito, sans-serif',
                'logo_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZ4K9chMtUiYREJpv7tS7lqSoY4YP8idKEXQ&s',
                'favicon_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZ4K9chMtUiYREJpv7tS7lqSoY4YP8idKEXQ&s',
                'custom_css' => null,
                'custom_js' => null,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('store_settings')->insert($configs);
    }
}
