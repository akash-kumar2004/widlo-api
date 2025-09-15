<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;
    public $expiryMinutes;
    public $supportUrl;

    public function __construct($otp, $userName, $expiryMinutes = 5, $supportUrl = null)
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->expiryMinutes = $expiryMinutes;
        $this->supportUrl = $supportUrl ?? config('app.url') . '/support';
    }

    public function build()
    {
        return $this->subject('Your OTP Code')
            ->markdown('emails.otp-verification')
            ->with([
                'otp' => $this->otp,
                'userName' => $this->userName,
                'expiryMinutes' => $this->expiryMinutes,
                'supportUrl' => $this->supportUrl
            ]);
    }
}
