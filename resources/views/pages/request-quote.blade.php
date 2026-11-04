@extends('layouts.app')
@section('title', 'Request a Quote')
@section('description', 'Request pricing and availability for fresh produce and agricultural products from Bangladesh.')
@section('content')
<x-page-banner title="Request a Quote" subtitle="Tell us what your business needs." tone="produce" />

<section class="section-shell">
    <div class="container-shell quote-layout">
        <aside class="quote-intro">
            <p class="eyebrow">Let’s work together</p>
            <h2>Tell us about your requirements.</h2>
            <p>Share the product, quantity and destination you have in mind. Our export team will review your request and follow up with availability and next steps.</p>
            <x-image-placeholder class="quote-intro__image" label="Fresh export produce photography pending" tone="produce" />
            <div class="quote-points">
                <p><span aria-hidden="true">✓</span> Product and seasonal availability</p>
                <p><span aria-hidden="true">✓</span> Buyer-specific packing options</p>
                <p><span aria-hidden="true">✓</span> Clear export coordination</p>
            </div>
        </aside>

        <div class="form-panel form-panel--quote">
            <div class="form-panel__heading"><p class="eyebrow">Buyer inquiry form</p><h2>Quote Request</h2><p>Fields marked <span aria-hidden="true">*</span> are required.</p></div>
            @if (session('status'))
                <p role="status" class="form-alert form-alert--success">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <div role="alert" class="form-alert form-alert--error">Please review the highlighted fields and try again.</div>
            @endif

            <form method="POST" action="{{ route('request-quote.submit') }}" class="form-grid">
                @csrf
                <div class="honeypot-field" aria-hidden="true">
                    <label for="quote-website">Leave this field empty</label>
                    <input id="quote-website" type="text" name="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="form-field">
                    <label for="quote-name">Full Name <span aria-hidden="true">*</span></label>
                    <input id="quote-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" placeholder="Your full name" @error('name') aria-invalid="true" @enderror>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="quote-company">Company Name <span class="field-optional">(Optional)</span></label>
                    <input id="quote-company" name="company" value="{{ old('company') }}" maxlength="180" autocomplete="organization" placeholder="Your company">
                    @error('company')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="quote-email">Email Address <span aria-hidden="true">*</span></label>
                    <input id="quote-email" type="email" name="email" value="{{ old('email') }}" required maxlength="254" autocomplete="email" placeholder="you@company.com" @error('email') aria-invalid="true" @enderror>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="quote-phone">Phone Number <span class="field-optional">(Optional)</span></label>
                    <input id="quote-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="50" autocomplete="tel" placeholder="+880 1XXX XXXXXX">
                    @error('phone')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="quote-country">Destination Country <span aria-hidden="true">*</span></label>
                    <input id="quote-country" name="country" value="{{ old('country') }}" required maxlength="100" autocomplete="country-name" placeholder="Where should we ship?" @error('country') aria-invalid="true" @enderror>
                    @error('country')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="quote-product">Product <span class="field-optional">(Optional)</span></label>
                    <select id="quote-product" name="product_id" @error('product_id') aria-invalid="true" @enderror>
                        <option value="">Select a product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) old('product_id', $selectedProductId) === (string) $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="quote-quantity">Estimated Quantity <span aria-hidden="true">*</span></label>
                    <input id="quote-quantity" type="number" name="quantity" value="{{ old('quantity') }}" min="0.001" step="0.001" required placeholder="e.g. 500" @error('quantity') aria-invalid="true" @enderror>
                    @error('quantity')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="quote-unit">Unit <span aria-hidden="true">*</span></label>
                    <select id="quote-unit" name="unit" required @error('unit') aria-invalid="true" @enderror>
                        @foreach (['kg' => 'Kilograms', 'tonnes' => 'Tonnes', 'cartons' => 'Cartons', 'containers' => 'Containers'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('unit', 'kg') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('unit')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field form-field--full">
                    <label for="quote-message">Additional Details <span class="field-optional">(Optional)</span></label>
                    <textarea id="quote-message" name="message" rows="4" maxlength="5000" placeholder="Product specifications, timing or other requirements...">{{ old('message') }}</textarea>
                    @error('message')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                @if ($turnstileSiteKey)
                    <div class="form-field form-field--full">
                        <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
                        @error('cf-turnstile-response')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                @elseif (app()->isProduction())
                    <p role="alert" class="field-error form-field--full">Online form security is not configured. Please contact us using the details in the footer.</p>
                @endif
                <div class="form-field--full">
                    <button type="submit" class="button button--primary button--wide">Submit Quote Request <span aria-hidden="true">→</span></button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@if ($turnstileSiteKey)
    @push('head')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush
@endif
