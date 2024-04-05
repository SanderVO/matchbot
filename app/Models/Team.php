<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Result relationship
     *
     * @return HasMany
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function results(): HasMany
    {
        return $this->hasMany(TeamResult::class, 'team_id');
    }

    /**
     * Team User relationship
     *
     * @return HasOne
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function teamUser(): HasOne
    {
        return $this->hasOne(TeamUser::class, 'team_id');
    }

    /**
     * Team Users relationship
     *
     * @return HasMany
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function teamUsers(): HasMany
    {
        return $this->hasMany(TeamUser::class, 'team_id');
    }

    /**
     * Users relationship
     *
     * @return BelongsToMany
     * 
     * @author Sander van Ooijen <sandervo+github@proton.me>
     * @version 1.0.0
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, TeamUser::class);
    }
}
