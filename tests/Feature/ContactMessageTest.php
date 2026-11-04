<?php

namespace Tests\Feature;

use App\Filament\Exports\ContactMessageExporter;
use App\Mail\SubmissionNotification;
use App\Models\ContactMessage;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders_a_working_submission_form(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee(route('contact.submit'), false)
            ->assertSee('name="message"', false)
            ->assertSee('name="website"', false);
    }

    public function test_valid_contact_submission_is_stored_and_queues_admin_notification(): void
    {
        Mail::fake();

        $this->post(route('contact.submit'), [
            'name' => 'Import Buyer',
            'email' => 'buyer@example.com',
            'phone' => '+880123456789',
            'subject' => 'Fresh mangoes',
            'message' => 'Please send your export catalogue.',
            'status' => 'replied',
            'ip' => '203.0.113.10',
        ])->assertRedirect(route('contact'))
            ->assertSessionHas('status', 'Thank you. Your message has been received.');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Import Buyer',
            'email' => 'buyer@example.com',
            'status' => ContactMessage::STATUS_NEW,
            'ip' => '127.0.0.1',
            'read_at' => null,
        ]);
        Mail::assertQueued(
            SubmissionNotification::class,
            fn (SubmissionNotification $mail): bool => $mail->type === 'contact message'
                && $mail->submission['subject'] === 'Fresh mangoes',
        );
    }

    public function test_missing_required_contact_fields_do_not_create_or_notify(): void
    {
        Mail::fake();

        $this->from(route('contact'))->post(route('contact.submit'), [])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingQueued();
    }

    public function test_honeypot_contact_submission_is_accepted_without_persisting(): void
    {
        Mail::fake();

        $this->post(route('contact.submit'), ['website' => 'automated submission'])
            ->assertRedirect(route('contact'))
            ->assertSessionHas('status', 'Thank you. Your submission has been received.');

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingQueued();
    }

    public function test_invalid_turnstile_token_rejects_contact_submission(): void
    {
        config([
            'services.turnstile.site_key' => 'site-key',
            'services.turnstile.secret_key' => 'secret-key',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => false]),
        ]);
        Mail::fake();

        $this->post(route('contact.submit'), [
            'name' => 'Import Buyer',
            'email' => 'buyer@example.com',
            'subject' => 'Fresh mangoes',
            'message' => 'Please send your export catalogue.',
            'cf-turnstile-response' => 'invalid-token',
        ])->assertSessionHasErrors(['cf-turnstile-response']);

        Http::assertSent(fn (ClientRequest $request): bool => $request->data()['secret'] === 'secret-key'
            && $request->data()['response'] === 'invalid-token');
        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingQueued();
    }

    public function test_contact_form_rate_limit_rejects_the_sixth_request(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('contact.submit'), ['website' => 'bot'])->assertRedirect(route('contact'));
        }

        $this->post(route('contact.submit'), ['website' => 'bot'])->assertTooManyRequests();
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_csv_exporter_neutralizes_spreadsheet_formulas(): void
    {
        $exporter = new ContactMessageExporter(new Export, ['name' => 'Name'], []);

        $row = $exporter(new ContactMessage(['name' => '=HYPERLINK("https://example.com")']));

        $this->assertSame(["'=HYPERLINK(\"https://example.com\")"], $row);
    }

    public function test_submission_notification_escapes_visitor_content(): void
    {
        $html = (new SubmissionNotification('contact message', [
            'name' => '<script>alert(1)</script>',
            'message' => '<img src=x onerror=alert(1)>',
        ]))->render();

        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
    }
}
