<?php

namespace App\Mail;

use App\Models\AutomationWalletFunding;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutomationLowBalanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AutomationWalletFunding $funding) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Automation balance is low: '.$this->funding->automation->automation_name);
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.automation.low-balance');
    }

    public function attachments(): array
    {
        return [];
    }
}
