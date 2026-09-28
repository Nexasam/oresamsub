<?php

namespace App\Mail;

use App\Models\AirtimeToCashRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AirtimeToCashSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AirtimeToCashRequest $requestRecord) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address((string) env('MAIL_FROM_ADDRESS'), (string) env('APP_NAME')),
            subject: 'New Airtime-to-Cash Request: '.$this->requestRecord->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.info.airtime_to_cash_submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
