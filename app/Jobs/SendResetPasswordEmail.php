<?php

namespace App\Jobs;

use App\Models\Member;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordEmail extends Job
{
    /**
     * @var
     */
    private $member;

    /**
     * Create a new job instance.
     *
     * @param Member $member
     */
    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $member = $this->member;
        $link_reset_password = env('MAIN_DOMAIN') . 'reset-password?email=' . $member->email . '&token=' . $member->reset_token;
         $memberData = [
            'fullname' => $member->fullname,
            'email'    => $member->email,
            'link_reset_password' => env('MAIN_DOMAIN').$link_reset_password
        ];
        Mail::send('emails.reset_password', $memberData, function ($mail) use ($member) {
            $subject = 'Reset Password';
            $mail->to($member->email, $member->fullname)
                ->subject($subject)
                ->getHeaders()
                ->addTextHeader('X-SMTPAPI', '{"category": "Forgot Password"}');
        });
    }
}
