<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class WhatsappLinkOtpMail extends Mailable
{
    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('OresamSub WhatsApp Verification OTP')
            ->view('emails.whatsapp_link_otp');
    }
}