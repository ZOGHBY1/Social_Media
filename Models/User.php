<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'gender',
        'region',
        'birthyear',
        'birthday',
        'birthmonth',
        'phonenumber',
        'category',
        'profilestatus',
        'ban',
        'banReason',
        'banDurationByDays',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function friends(){

        return $this->hasMany(Friends::class,'userid');
    }

    public function posts(){

        return $this->hasMany(Posts::class,'userid');
    }

    public function likes(){

        return $this->hasMany(Likes::class,'userid');

    }

    public function comment(){

        return $this->hasMany(Comments::class,'userid');

    }

    public function report(){

        return $this->hasMany(Reports::class,'userid');

    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
