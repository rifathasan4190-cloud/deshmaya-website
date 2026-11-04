<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $submission
     */
    public function __construct(
        public readonly string $type,
        public readonly array $submission,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New '.ucfirst($this->type).' from '.($this->submission['name'] ?? 'a visitor'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.submissions.notification',
            with: [
                'type' => $this->type,
                'submission' => $this->submission,
            ],
        );
    }
}
