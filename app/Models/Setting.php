<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'wa_number',
        'address',
        'email',
        'company_description',
        'operating_hours',
    ];

    /**
     * Get the singleton settings row, creating it if it doesn't exist.
     */
    public static function getSetting(): static
    {
        return static::firstOrCreate([], [
            'wa_number' => '628000000000',
            'address' => 'Jl. Contoh No. 1, Kota, Provinsi',
            'email' => 'info@cvberkah.com',
            'company_description' => 'CV Berkah adalah perusahaan penjualan besi tua terpercaya.',
            'operating_hours' => 'Senin–Sabtu, 08.00–17.00',
        ]);
    }
}
