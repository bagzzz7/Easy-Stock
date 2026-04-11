<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_STAFF = 'staff';
    const ROLE_PHARMACIST = 'pharmacist';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_photo',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Role Check Methods
    public function isAdministrator(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isPharmacist(): bool
    {
        return $this->role === self::ROLE_PHARMACIST;
    }

    // ✅ ADD THIS MISSING METHOD
    public function isManagement(): bool
    {
        return in_array($this->role, [self::ROLE_ADMINISTRATOR, self::ROLE_STAFF]);
    }

    // Permission Methods
    public function canManageUsers(): bool
    {
        return $this->isAdministrator() || $this->isStaff();
    }

    public function canManageSuppliers(): bool
    {
        return $this->isManagement() || $this->isPharmacist();
    }

    public function canManageCategories(): bool
    {
        return $this->isManagement();
    }

    public function canViewReports(): bool
    {
        return true; // All users can view reports
    }

    public function canProcessSales(): bool
    {
        return true; // All users can process sales
    }

    // Accessors
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/profile-photos/' . $this->profile_photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getRoleDisplayNameAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMINISTRATOR => 'Administrator',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_PHARMACIST => 'Pharmacist',
            default => 'Unknown',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMINISTRATOR => 'bg-danger',
            self::ROLE_STAFF => 'bg-warning text-dark',
            self::ROLE_PHARMACIST => 'bg-info',
            default => 'bg-secondary',
        };
    }

    // Relationships
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function chatbotLogs()
    {
        return $this->hasMany(ChatbotLog::class);
    }
}