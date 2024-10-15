<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use MailerSend\LaravelDriver\MailerSendTrait;

class UserRegistrationNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // MailerSendTrait
    
    /**
     * Create a new message instance.
     */
    public function __construct(public array $data)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address(env('MAIL_FROM_ADDRESS'), ''),
            ],
            subject: $this->data['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        
        $url = env('APP_URL');
        return new Content(
            markdown: 'emails.user_registration_notification',
            with: [
                'content' => $this->data['content'],
                'website_url' => $url

            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
