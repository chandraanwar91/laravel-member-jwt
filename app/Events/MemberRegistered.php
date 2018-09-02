<?php

namespace App\Events;

use App\Models\Member;

class MemberRegistered extends Event
{
    public $member;

    /**
     * Create a new event instance.
     *
     * @param Member $member
     */
    public function __construct(Member $member)
    {
        $this->member = $member;
    }
}
