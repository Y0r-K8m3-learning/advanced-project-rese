<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use Billable;
    // 役割を定数で管理
    const ROLE_USER = 10;
    const ROLE_OWNER = 20;
    const ROLE_ADMIN = 30;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function restaurant()
    {
        return $this->hasMany(Restaurant::class);
    }
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // 管理者かどうかを確認
    public function isAdmin(): bool
    {
        return $this->role_id === self::ROLE_ADMIN;
    }

    // オーナーかどうかを確認
    public function isOwner(): bool
    {
        return $this->role_id === self::ROLE_OWNER;
    }

    // 一般ユーザーかどうかを確認
    public function isUser(): bool
    {
        return $this->role_id === self::ROLE_USER;
    }
}
