<?php

namespace App\Http\Requests;

use App\Rules\VerifyTurnstile;
use Illuminate\Foundation\Http\FormRequest;

abstract class SubmissionFormRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    protected function turnstileRules(): array
    {
        $isConfigured = filled(config('services.turnstile.site_key'))
            || filled(config('services.turnstile.secret_key'));

        return [
            'cf-turnstile-response' => [
                app()->isProduction() || $isConfigured ? 'required' : 'nullable',
                'string',
                'max:2048',
                new VerifyTurnstile,
            ],
        ];
    }
}
