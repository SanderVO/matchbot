<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'authorization_type',
        'authorization_token',
        'authorization_user_id',
        'organization_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'authorization_type',
        'authorization_token',
        'authorization_user_id'
    ];

    /**
     * Organization relationship
     *
     * @return BelongsTo
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Team result users relationship
     *
     * @return HasMany
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function teamResultUsers(): HasMany
    {
        return $this->hasMany(TeamResultUser::class, 'user_id');
    }

    /**
     * Teams relationship
     *
     * @return HasManyThrough
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function teams(): HasManyThrough
    {
        return $this->hasManyThrough(Team::class, 'team_user');
    }
}
