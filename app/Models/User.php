<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasCompanyScope;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'company_id',
        'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'role'              => Role::class,
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isManager(): bool
    {
        return $this->role === Role::Manager;
    }

    public function isEmployee(): bool
    {
        return $this->role === Role::Employee;
    }
}
