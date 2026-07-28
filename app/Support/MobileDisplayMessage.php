<?php

namespace App\Support;

use Illuminate\Support\Str;

final class MobileDisplayMessage
{
    public static function clean(mixed $value, ?string $fallback = null): ?string
    {
        if (! is_scalar($value)) {
            return $fallback;
        }

        $message = html_entity_decode(trim((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($message === '') {
            return $fallback;
        }

        if (preg_match('/<!doctype|<html\b|<head\b|<script\b|<style\b|stack\s*trace|sqlstate|vendor\/laravel/i', $message)) {
            return $fallback;
        }

        $message = strip_tags($message);
        $message = preg_replace('/\s+/u', ' ', $message) ?? '';
        $message = trim($message);

        return $message === '' ? $fallback : Str::limit($message, 220, '…');
    }
}
