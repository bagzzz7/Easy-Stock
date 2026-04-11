<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Scope for recent activities
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
    
    // Scope for specific user
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    // Scope for specific action
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }
}