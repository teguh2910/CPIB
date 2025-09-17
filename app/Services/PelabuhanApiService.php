<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class PelabuhanApiService
{
    /**
     * Get the current API token from session.
     */
    public static function getToken(): ?string
    {
        return Session::get('access_token');
    }

    /**
     * Check if token exists and is valid (not expired).
     */
    public static function hasValidToken(): bool
    {
        $token = self::getToken();
        $expiresAt = Session::get('token_expires_at');

        return ! empty($token) && ! empty($expiresAt) && now()->timestamp < (int) $expiresAt;
    }

    /**
     * Store token in session.
     */
    public static function storeToken(string $token, int $expiresIn): void
    {
        Session::put('access_token', $token);
        Session::put('token_expires_at', now()->addSeconds($expiresIn)->timestamp);
    }

    /**
     * Clear token from session.
     */
    public static function clearToken(): void
    {
        Session::forget(['access_token', 'token_expires_at']);
    }

    /**
     * Get the API base URL.
     */
    public static function getApiUrl(): string
    {
        return (string) config('services.pelabuhan_api.url');
    }
}
