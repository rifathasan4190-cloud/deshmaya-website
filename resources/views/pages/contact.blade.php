@extends('layouts.app')
@section('title', 'Contact Us')
@section('description', 'Contact Deshmaya to discuss agricultural sourcing, export requirements and business inquiries.')
@section('content')
<x-page-banner title="Contact Us" subtitle="Let’s talk about your requirements." tone="land" />

<section class="section-shell">
    <div class="container-shell contact-layout">
        <aside class="contact-sidebar">
            <p class="eyebrow">Get in touch</p>
            <h2>We’re here to help.</h2>
            <p>Tell us what you’re looking for and our team will get back to you.</p>
            <div class="contact-details">
                @if($address = setting('contact.address'))
                    <div class="contact-detail"><span class="contact-detail__icon" aria-hidden="true">⌖</span><div><strong>Visit Us</strong><p>{{ $address }}</p></div></div>
                @endif
                @if($phone = setting('contact.phone_primary'))
                    <div class="contact-detail"><span class="contact-detail__icon" aria-hidden="true">☎</span><div><strong>Call Us</strong><a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a></div></div>
                @endif
                @if($email = setting('contact.email_primary'))
                    <div class="contact-detail"><span class="contact-detail__icon" aria-hidden="true">✉</span><div><strong>Email Us</strong><a href="mailto:{{ $email }}">{{ $email }}</a></div></div>
                @endif
            </div>
            <x-image-placeholder class="contact-map" label="Map artwork pending" tone="markets" />
            <div class="contact-social">
                <strong>Follow Deshmaya</strong>
                <div class="social-links">
                    @foreach(['facebook', 'instagram', 'linkedin', 'youtube'] as $network)
                        @if($url = setting("social.$network"))
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network) }}">{{ strtoupper(substr($network, 0, 1)) }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        </aside>

        <div class="form-panel">
            <div class="form-panel__heading"><p class="eyebrow">Send us a message</p><h2>How can we help?</h2></div>
            @if (session('status'))
                <p role="status" class="form-alert form-alert--success">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <div role="alert" class="form-alert form-alert--error">Please review the highlighted fields and try again.</div>
            @endif

            <form method="POST" action="{{ route('contact.submit') }}" class="form-grid">
                @csrf
                <div class="honeypot-field" aria-hidden="true">
                    <label for="contact-website">Leave this field empty</label>
                    <input id="contact-website" type="text" name="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="form-field">
                    <label for="contact-name">Full Name <span aria-hidden="true">*</span></label>
                    <input id="contact-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" placeholder="Your full name" @error('name') aria-invalid="true" @enderror>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="contact-email">Email Address <span aria-hidden="true">*</span></label>
                    <input id="contact-email" type="email" name="email" value="{{ old('email') }}" required maxlength="254" autocomplete="email" placeholder="you@company.com" @error('email') aria-invalid="true" @enderror>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="contact-phone">Phone Number <span class="field-optional">(Optional)</span></label>
                    <input id="contact-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="50" autocomplete="tel" placeholder="+880 1XXX XXXXXX" @error('phone') aria-invalid="true" @enderror>
                    @error('phone')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field">
                    <label for="contact-subject">Subject <span aria-hidden="true">*</span></label>
                    <input id="contact-subject" name="subject" value="{{ old('subject') }}" required maxlength="180" placeholder="How can we help?" @error('subject') aria-invalid="true" @enderror>
                    @error('subject')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-field form-field--full">
                    <label for="contact-message">Your Message <span aria-hidden="true">*</span></label>
                    <textarea id="contact-message" name="message" rows="5" required maxlength="5000" placeholder="Tell us a little about your inquiry..." @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
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
                    <button type="submit" class="button button--primary">Send Message <span aria-hidden="true">→</span></button>
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
