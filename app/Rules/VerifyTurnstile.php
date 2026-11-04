<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyTurnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.turnstile.secret_key');

        if (blank($secret)) {
            $fail('The security verification is not configured.');

            return;
        }

        try {
            $response = Http::asForm()
                ->connectTimeout(3)
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Cloudflare Turnstile verification could not connect.', [
                'exception' => $exception::class,
            ]);
            $fail('The security check could not be verified. Please try again.');

            return;
        }

        if (! $response->successful() || $response->json('success') !== true) {
            $fail('Please complete the security check.');
        }
    }
}
