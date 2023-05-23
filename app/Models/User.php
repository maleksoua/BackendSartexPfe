<?php

namespace App\Models;

use App\Notifications\MailResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @property int id
 * @property string profile_image
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property string phone
 * @property string register_number
 * @property int role
 * @property int super_chef_id
 * @property string password
 * @property int created_at
 * @property int updated_at
 *
 * @property User superChef
 * @property Site site
 * @property Collection chefs
 * @property Collection zones
 * @property Collection plannings
 *
 * @package App
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 1;
    const ROLE_CHEF = 2;
    const ROLE_SUPER_CHEF = 3;

    const ROLES = [self::ROLE_ADMIN, self::ROLE_CHEF, self::ROLE_SUPER_CHEF];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return HasOne
     */
    public function site()
    {
        return $this->hasOne(Site::Class, 'super_chef_id');
    }

    /**
     * @return HasMany
     */
    public function zones()
    {

        return $this->hasMany(Zone::Class, 'chef_id');
    }

    /**
     * @return BelongsTo
     */
    public function superChef()
    {
        return $this->belongsTo(User::Class, 'super_chef_id');
    }

    /**
     * @return HasMany
     */
    public function chefs()
    {
        return $this->hasMany(User::Class, 'super_chef_id');
    }

    /**
     * @return HasMany
     */
    public function plannings()
    {
        return $this->hasMany(Planning::Class, 'chef_id');
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordNotification($token));
    }
}
