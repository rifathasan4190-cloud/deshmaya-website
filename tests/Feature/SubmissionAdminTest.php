<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Admin\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_submission_counts_and_latest_records(): void
    {
        $admin = $this->createAdmin();
        $contact = ContactMessage::query()->create([
            'name' => 'Contact Buyer',
            'email' => 'contact@example.com',
            'subject' => 'Product enquiry',
            'message' => 'Please send a catalogue.',
        ]);
        $quote = QuoteRequest::query()->create([
            'name' => 'Quote Buyer',
            'email' => 'quote@example.com',
            'country' => 'Canada',
            'product_name' => 'Export Mangoes',
            'quantity' => '100',
            'unit' => 'kg',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('New contact messages')
            ->assertSee('New quote requests')
            ->assertSee('Latest submissions')
            ->assertSee($contact->name)
            ->assertSee($quote->name)
            ->assertSee('Export Mangoes');
    }

    public function test_admin_can_find_and_update_submission_statuses(): void
    {
        $admin = $this->createAdmin();
        $contact = ContactMessage::query()->create([
            'name' => 'Contact Buyer',
            'email' => 'contact@example.com',
            'subject' => 'Product enquiry',
            'message' => 'Please send a catalogue.',
        ]);
        $quote = QuoteRequest::query()->create([
            'name' => 'Quote Buyer',
            'email' => 'quote@example.com',
            'country' => 'Canada',
            'quantity' => '10',
            'unit' => 'kg',
        ]);
        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(ListContactMessages::class)
            ->assertCanSeeTableRecords([$contact])
            ->assertTableHeaderActionsExistInOrder(['export'])
            ->callTableAction('update_status', $contact, data: ['status' => ContactMessage::STATUS_REPLIED])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('contact_messages', [
            'id' => $contact->id,
            'status' => ContactMessage::STATUS_REPLIED,
        ]);

        Livewire::actingAs($admin)
            ->test(ListQuoteRequests::class)
            ->assertCanSeeTableRecords([$quote])
            ->assertTableHeaderActionsExistInOrder(['export'])
            ->callTableAction('update_status', $quote, data: ['status' => QuoteRequest::STATUS_QUOTED])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('quote_requests', [
            'id' => $quote->id,
            'status' => QuoteRequest::STATUS_QUOTED,
        ]);
    }

    private function createAdmin(): User
    {
        $admin = User::query()->create([
            'name' => 'Submission Admin',
            'email' => 'submission-admin@example.com',
            'password' => 'test-password',
        ]);
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }
}
