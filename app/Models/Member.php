<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\MemberProfile;

/**
 * Class Member
 *
 * @package App\Models
 */
class Member extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    const AUTHENTICATION_VALID      = 'valid';
    const AUTHENTICATION_PENDING    = 'pending';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'fullname',
        'email',
        'password',
        'profile_id',
        'status',
        'source',
        'authentication',
        'verification_token',
        'reset_token'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_login',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Always trim and store member's email as lower case
     *
     * @param $email
     */
    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = strtolower(trim($email));
    }

    /**
     * Hash member's password here
     * Update member hashed password to use stronger hash algorithm
     *
     * @param $pass
     */
    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = app('hash')->make($pass);
    }

    /**
     * Set gender as uppercase
     *
     * @param $gender
     */
    public function setGenderAttribute($gender)
    {
        $this->attributes['gender'] = empty($gender) ? 'M' : strtoupper($gender);
    }

    /**
     * If birthdate not provided, set as null instead
     *
     * @param $date
     */
    public function setBirthDateAttribute($date)
    {
        $this->attributes['birth_date'] = empty($date) ? null : $date;
    }

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'fullname' => $this->fullname,
            'email'    => $this->email,
        ];
    }

    /**
     *
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(MemberProfile::class, 'profile_id','id');
    }
}
