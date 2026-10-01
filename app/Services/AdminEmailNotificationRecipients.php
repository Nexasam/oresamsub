<?php

namespace App\Services;

use App\Models\AdminEmailNotificationPreference;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class AdminEmailNotificationRecipients
{
    public const TYPES = [
        'failed_transactions',
        'pending_transactions',
        'automation_low_balance',
    ];

    public function emails(string $type): Collection
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException("Unknown admin email notification type [{$type}].");
        }

        if (! Schema::hasTable('admin_email_notification_preferences')) {
            return $this->legacyEmails($type);
        }

        $preferences = AdminEmailNotificationPreference::query()->get()->keyBy('user_id');
        $legacyEmails = $this->legacyEmails($type);

        return User::query()
            ->whereNotNull('email')
            ->where(fn ($query) => $query->whereNull('is_deactivated')->orWhere('is_deactivated', false))
            ->where(fn ($query) => $query
                ->whereHas('role', fn ($role) => $role->where('role_name', 'Admin'))
                ->orWhereHas('roles', fn ($role) => $role->where('role_name', 'Admin')))
            ->get(['id', 'email'])
            ->filter(function (User $admin) use ($type, $preferences, $legacyEmails): bool {
                $preference = $preferences->get($admin->id);
                if ($preference) {
                    return (bool) $preference->{$type};
                }

                return $type === 'automation_low_balance'
                    || $legacyEmails->contains(strtolower(trim((string) $admin->email)));
            })
            ->pluck('email')
            ->filter()
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->unique()
            ->values();
    }

    private function legacyEmails(string $type): Collection
    {
        if ($type === 'automation_low_balance') {
            return User::query()
                ->whereNotNull('email')
                ->where(fn ($query) => $query->whereNull('is_deactivated')->orWhere('is_deactivated', false))
                ->where(fn ($query) => $query
                    ->whereHas('role', fn ($role) => $role->where('role_name', 'Admin'))
                    ->orWhereHas('roles', fn ($role) => $role->where('role_name', 'Admin')))
                ->pluck('email')
                ->map(fn ($email) => strtolower(trim((string) $email)))
                ->unique()
                ->values();
        }

        return collect(explode(',', (string) Setting::query()
            ->where('field_name', 'emails_to_notify_failed_transactions')
            ->value('field_value')))
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();
    }
}
