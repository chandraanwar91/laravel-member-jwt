<?php
namespace App\Transformers;

use App\Models\Member;
use League\Fractal;

/**
 * Class MemberTransformer
 *
 * @package App\Transformers
 */
class MemberTransformer extends Fractal\TransformerAbstract
{
    /**
     * Transform response
     *
     * @param Member $member
     * @return array
     */
    public function transform(Member $member)
    {
        return [
            'id'                => (int) $member->id,
            'email'             => $member->email,
            'full_name'         => $member->fullname,
            'gender'            => $member->profile->gender,
            'birth_date'        => $member->profile->birth_date,
            'phone_number'      => $member->profile->phone_number,
            'secondary_contact' => $member->profile->secondary_contact,
            'photo'             => $member->profile->photo,
            'address'           => $member->profile->address,
            'authentication'    => $member->authentication,
            'status'            => $member->status,
            'created_at'        => $member->created_at->toIso8601String(),
            'updated_at'        => $member->updated_at ? $member->updated_at->toIso8601String() : null,
        ];
    }
}
