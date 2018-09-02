<?php

namespace App\Listeners;

use App\Events\MemberRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendWelcomeEmail
 *
 * @package App\Listeners
 */
class SendConfirmationEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  MemberRegistered $event
     * @return void
     */
    public function handle(MemberRegistered $event)
    {
        $member = $event->member;
        $link_confirm_password = env('MAIN_DOMAIN') . 'reset-password?email=' . $member->email . '&token=' . $member->verification_token;

        $memberData = [
            'fullname' => $member->fullname,
            'email'    => $member->email,
            'link_confirm_password' => env('MAIN_DOMAIN').$link_confirm_password
        ];

        Mail::send('emails.member_confirmation', $memberData, function ($mail) use ($member) {
            $subject = 'Confirm your registration';
            $mail->to($member->email, $member->fullname)
                ->subject($subject)
                ->getHeaders()
                ->addTextHeader('X-SMTPAPI', '{"category": "Member Registration"}');
        });
    }
}
