<?php

namespace App\Services;

use App\Mail\SubmissionNotification;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class SubmissionNotifier
{
    public function __construct(private readonly SettingService $settings) {}

    public function recipient(): string
    {
        $recipient = $this->settings->get('mail.notification_email')
            ?: config('mail.from.address');

        if (! is_string($recipient) || filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
            throw new RuntimeException('Configure a valid admin notification email in Business & Connect Info or MAIL_FROM_ADDRESS.');
        }

        return $recipient;
    }

    /**
     * @param  array<string, mixed>  $submission
     */
    public function notify(string $recipient, string $type, array $submission): void
    {
        Mail::to($recipient)->queue(
            (new SubmissionNotification($type, $submission))->afterCommit(),
        );
    }
}
