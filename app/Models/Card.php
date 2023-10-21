<?php

namespace App\Models;

use App\Models\Category;
use App\Models\CardDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'disabled',
        'date_from',
        'date_to',
        'user_id',
        'creator_user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany
     */
    public function cardDetails(): HasMany
    {
        return $this->hasMany(CardDetail::class);
    }


    public function scopePermission($query, User $user)
    {
        switch($user->role){
            case 1: 
                return $query;
            case 2:
                return $query->where('creator_user_id', $user->id)->orWhere('user_id', $user->id);
            default:
                return $query->where('user_id', $user->id);
        }
    }
}
