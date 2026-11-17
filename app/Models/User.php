<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * tipo: 'vendedor' ou 'cliente'
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class, 'vendedor_id');
    }

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class, 'cliente_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
