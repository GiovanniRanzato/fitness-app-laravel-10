<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use DateTime;
use Spatie\Image\Manipulations;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;
/** 
* @method bool isAdmin()
*/
class User extends Authenticatable // implements HasMedia
{

    use HasApiTokens, HasFactory, Notifiable; // , InteractsWithMedia;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'role' => 0, // normal user
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_name',
        'phone',
        'birth_day',
        'sex',
        'weight',
        'height',
        'job',
        'country',
        'city',
        'postal_code',
        'address',
        'role',
        'avatar'
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
        'birth_day' => 'date'
    ];
    
    /**
     * Generatate appropriate token for based on user role.
     * @return string
     */
    public function createAuthToken(){
        $token = '';
        switch($this->role){
            case 1:
                $token = $this->createToken('admin-token', ['admin-abilities'])->plainTextToken; 
                break;
            case 2:
                $token = $this->createToken('trainer-token', ['trainer-abilities'])->plainTextToken; 
                break;
            default:
                $token = $this->createToken('user-token',['user-abilities'])->plainTextToken;
        }
        return $token;
    }

    /** 
     * Relationships
     */
    public function category(){
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }


    /**
     * Tell if user is admin or not
     * @return boolean
     */
    public function isAdmin(){
        return $this->role === "1" && $this->tokenCan('admin-abilities');
    }
    /**
     * Tell if user is a trainer or not
     * @return boolean
     */
    public function isTrainer(){
        return $this->role === "2" && $this->tokenCan('trainer-abilities');
    }


    /**
     * The Collection component will show a preview thumbnail for items in the collection it is showing.
     * To generate that thumbnail, you must add a conversion like this one to your model.
     * @param Spatie\MediaLibrary\MediaCollections\Models\Media
     * @return boolean
     */

    // public function registerMediaConversions(Media $media = null): void
    // {
    //     $this
    //         ->addMediaConversion('preview')
    //         ->fit(Manipulations::FIT_CROP, 300, 300)
    //         ->nonQueued();
    // }
}