<?php

namespace Tests\Feature;

use App\Mail\SubmissionNotification;
use App\Models\Category;
use App\Models\Product;
use App\Models\QuoteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuoteRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_form_prefills_an_active_product_from_its_slug(): void
    {
        $product = $this->createProduct('Export Mangoes');

        $this->get(route('request-quote', ['product' => $product->slug]))
            ->assertOk()
            ->assertSee('Export Mangoes')
            ->assertSee('value="'.$product->id.'" selected', false);
    }

    public function test_valid_quote_request_is_stored_with_product_snapshot_and_notifies_admin(): void
    {
        $product = $this->createProduct('Export Mangoes');
        config([
            'services.turnstile.site_key' => 'site-key',
            'services.turnstile.secret_key' => 'secret-key',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true]),
        ]);
        Mail::fake();

        $this->post(route('request-quote.submit'), [
            'name' => 'Import Buyer',
            'company' => 'Global Foods',
            'email' => 'buyer@example.com',
            'phone' => '+880123456789',
            'country' => 'Canada',
            'product_id' => $product->id,
            'quantity' => '12.5',
            'unit' => 'tonnes',
            'message' => 'Please include shipping options.',
            'cf-turnstile-response' => 'valid-token',
            'status' => 'quoted',
        ])->assertRedirect(route('request-quote'))
            ->assertSessionHas('status', 'Thank you. Your quote request has been received.');

        $this->assertDatabaseHas('quote_requests', [
            'name' => 'Import Buyer',
            'product_id' => $product->id,
            'product_name' => 'Export Mangoes',
            'quantity' => '12.500',
            'unit' => 'tonnes',
            'status' => QuoteRequest::STATUS_NEW,
        ]);
        Http::assertSent(fn ($request): bool => $request->data()['response'] === 'valid-token');
        Mail::assertQueued(
            SubmissionNotification::class,
            fn (SubmissionNotification $mail): bool => $mail->type === 'quote request'
                && $mail->submission['product_name'] === 'Export Mangoes',
        );
    }

    public function test_quote_request_validation_rejects_invalid_quantity_and_inactive_product(): void
    {
        $product = $this->createProduct('Inactive Export Product', ['is_active' => false]);
        Mail::fake();

        $this->post(route('request-quote.submit'), [
            'name' => 'Import Buyer',
            'email' => 'buyer@example.com',
            'country' => 'Canada',
            'product_id' => $product->id,
            'quantity' => '0',
            'unit' => 'kg',
        ])->assertSessionHasErrors(['product_id', 'quantity']);

        $this->assertDatabaseCount('quote_requests', 0);
        Mail::assertNothingQueued();
    }

    public function test_deleting_a_product_preserves_its_quote_product_snapshot(): void
    {
        $product = $this->createProduct('Export Mangoes');
        $quote = QuoteRequest::query()->create([
            'name' => 'Import Buyer',
            'email' => 'buyer@example.com',
            'country' => 'Canada',
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => '10',
            'unit' => 'kg',
        ]);

        $product->forceDelete();
        $quote->refresh();

        $this->assertNull($quote->product_id);
        $this->assertNull($quote->product);
        $this->assertSame('Export Mangoes', $quote->product_name);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createProduct(string $name, array $attributes = []): Product
    {
        $category = Category::query()->create([
            'name' => 'Quote category '.(Category::query()->count() + 1),
        ]);

        return Product::query()->create(array_merge([
            'category_id' => $category->id,
            'name' => $name,
        ], $attributes));
    }
}
