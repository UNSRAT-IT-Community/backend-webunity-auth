<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nim',
        'profile_picture',
        'email',
        'password',
        'role_id',
        'division_id',
        'is_accepted'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles():BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function divisions():BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'creator_id', 'id');
    }

    public function upcomingEvents(): HasMany
    {
        return $this->hasMany(UpcomingEvent::class, 'creator_id', 'id');
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'creator_id', 'id');
    }

    public function communityAds(): HasMany
    {
        return $this->hasMany(CommunityAd::class, 'creator_id', 'id');
    }

    public function chatbots(): HasMany
    {
        return $this->hasMany(Chatbot::class, 'user_id', 'id');
    }

    /**
     * Scope a query to only include specific columns.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSelectMinimal($query)
    {
        return $query->select('id', 'name', 'profile_picture', 'email', 'role_id', 'division_id');
    }
}