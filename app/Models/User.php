<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class, 'created_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class, 'created_by');
    }
}
