<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MemberProfile
 *
 * @package App\Models
 */
class MemberProfile extends Model
{

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'gender',
        'birth_date',
        'phone_number',
        'secondary_contact',
        'photo',
        'address',
    ];

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
}
