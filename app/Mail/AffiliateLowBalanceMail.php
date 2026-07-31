<?php

namespace App\Mail;

use App\Models\AffiliateWalletSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AffiliateLowBalanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AffiliateWalletSetting $setting,
        public float $walletBalance
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your OresamSub wallet balance is low');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.affiliate.low-balance');
    }

    public function attachments(): array
    {
        return [];
    }
}
