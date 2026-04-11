<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'license_number',      // Only license number added
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
}