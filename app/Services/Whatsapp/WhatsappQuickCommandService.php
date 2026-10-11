<?php

namespace App\Services\Whatsapp;

use App\Models\User;
use App\Models\WhatsappQuickCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WhatsappQuickCommandService
{
    public function record(?User $user, string $whatsappPhone, ?string $command, ?string $type = null): void
    {
        $command = $this->normalizeCommand($command);

        if ($command === null || ! $this->isRecordable($command)) {
            return;
        }

        $hash = hash('sha256', Str::lower($command));

        $existing = WhatsappQuickCommand::query()
            ->where('whatsapp_phone', $whatsappPhone)
            ->where('command_hash', $hash)
            ->first();

        if ($existing) {
            $existing->update([
                'user_id' => $user?->id ?? $existing->user_id,
                'command_type' => $type ?: $existing->command_type,
                'label' => $this->label($command),
                'usage_count' => $existing->usage_count + 1,
                'last_used_at' => now(),
            ]);

            return;
        }

        WhatsappQuickCommand::query()->create([
            'user_id' => $user?->id,
            'whatsapp_phone' => $whatsappPhone,
            'command_hash' => $hash,
            'command' => $command,
            'command_type' => $type,
            'label' => $this->label($command),
            'last_used_at' => now(),
        ]);
    }

    public function recent(?User $user, string $whatsappPhone, int $limit = 8): Collection
    {
        return WhatsappQuickCommand::query()
            ->where(function ($query) use ($user, $whatsappPhone) {
                $query->where('whatsapp_phone', $whatsappPhone);

                if ($user) {
                    $query->orWhere('user_id', $user->id);
                }
            })
            ->latest('last_used_at')
            ->limit($limit)
            ->get();
    }

    public function findForReplay(string $id, ?User $user, string $whatsappPhone): ?WhatsappQuickCommand
    {
        return WhatsappQuickCommand::query()
            ->whereKey($id)
            ->where(function ($query) use ($user, $whatsappPhone) {
                $query->where('whatsapp_phone', $whatsappPhone);

                if ($user) {
                    $query->orWhere('user_id', $user->id);
                }
            })
            ->first();
    }

    public function replayId(string $id): string
    {
        return "repeat_cmd_{$id}";
    }

    public function idFromReplayText(string $text): ?string
    {
        return Str::startsWith($text, 'repeat_cmd_')
            ? Str::after($text, 'repeat_cmd_')
            : null;
    }

    private function normalizeCommand(?string $command): ?string
    {
        $command = trim((string) $command);
        $command = preg_replace('/\s+/', ' ', $command);

        return $command !== '' ? $command : null;
    }

    private function isRecordable(string $command): bool
    {
        $lower = Str::lower($command);

        if (in_array($lower, ['yes', 'no', 'start', 'hi', 'hello', 'recent', 'recent commands', 'buy again', 'favourites', 'favorites'], true)) {
            return false;
        }

        return Str::length($command) >= 6;
    }

    private function label(string $command): string
    {
        return Str::limit(Str::headline($command), 60, '');
    }
}
