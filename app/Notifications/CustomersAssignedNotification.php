<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class CustomersAssignedNotification extends Notification implements ShouldQueue {
    use Queueable;
    public function __construct(public int $customerCount) {}
    public function via(object $notifiable): array { return ['mail']; }
    public function toMail(object $notifiable): MailMessage {
        return (new MailMessage)->subject('New customers assigned to you')
            ->greeting('Hello '.$notifiable->first_name.',')
            ->line("{$this->customerCount} customer(s) have been assigned to your account-officer portfolio.")
            ->action('Open follow-up dashboard', route('admin.daily_customer_followup.index'));
    }
}
