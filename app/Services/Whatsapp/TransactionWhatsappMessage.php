<?php

namespace App\Services\Whatsapp;

use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionWhatsappMessage
{
    public function build(Transaction $transaction, string $notificationType): array
    {
        $status = $notificationType === 'refunded' ? 'refunded' : 'failed';
        $reason = $this->reason($transaction, $status);
        $service = $this->service($transaction);
        $amount = number_format((float) ($transaction->discounted_amount ?: $transaction->amount), 2);
        $recipient = $this->maskedRecipient($transaction);
        $reference = Str::limit((string) ($transaction->txn_reference ?: $transaction->id), 80, '');
        $name = Str::limit(trim((string) ($transaction->user?->first_name ?: 'Customer')), 40, '');

        $parameters = [$name, $service, $amount, $recipient, $reference, $reason];
        $headline = $status === 'refunded'
            ? '↩️ Transaction Refunded'
            : '❌ Transaction Failed';

        return [
            'parameters' => $parameters,
            'text' => implode("\n", [
                $headline,
                '',
                "Hello {$name},",
                "Service: {$service}",
                "Amount: ₦{$amount}",
                "Recipient: {$recipient}",
                "Reference: {$reference}",
                "Reason: {$reason}",
                '',
                $status === 'refunded'
                    ? 'The transaction amount has been returned to your OresamSub wallet.'
                    : 'If your wallet was debited, please check the transaction status for any refund update.',
                'Reply SUPPORT if you need help.',
            ]),
        ];
    }

    private function reason(Transaction $transaction, string $status): string
    {
        $value = $status === 'refunded' && filled($transaction->refund_reason)
            ? $transaction->refund_reason
            : $transaction->user_screen_message;

        if (is_string($value) && str_starts_with(ltrim($value), '{')) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded['message'] ?? $decoded['user_message'] ?? $decoded['detail'] ?? null;
            }
        }

        $reason = preg_replace('/\s+/', ' ', strip_tags((string) $value));
        $reason = trim((string) $reason);

        if ($reason === '') {
            $reason = $status === 'refunded'
                ? 'The transaction could not be completed and was refunded.'
                : 'The provider could not complete the transaction.';
        }

        return Str::limit($reason, 220, '…');
    }

    private function service(Transaction $transaction): string
    {
        $plan = trim((string) $transaction->product_plan?->product_plan_name);
        if ($plan !== '') {
            return Str::limit($plan, 100, '…');
        }

        return Str::limit(
            Str::headline((string) ($transaction->transaction_category ?: 'service')),
            100,
            '…'
        );
    }

    private function maskedRecipient(Transaction $transaction): string
    {
        $recipient = preg_replace('/\s+/', '', (string) (
            $transaction->phone_number
            ?: $transaction->smart_card_number
            ?: $transaction->metre_number
            ?: 'Not available'
        ));

        if (strlen($recipient) < 7) {
            return $recipient;
        }

        return substr($recipient, 0, 4).'***'.substr($recipient, -4);
    }
}
