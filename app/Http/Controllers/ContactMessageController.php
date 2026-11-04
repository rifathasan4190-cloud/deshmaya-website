<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Services\SubmissionNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function create(): View
    {
        return view('pages.contact', [
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ]);
    }

    public function store(
        StoreContactMessageRequest $request,
        SubmissionNotifier $notifier,
    ): RedirectResponse {
        $recipient = $notifier->recipient();
        $data = $request->safe()->only(['name', 'email', 'phone', 'subject', 'message']);

        ContactMessage::query()->create($data + [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
                ? Str::limit($request->userAgent(), 1000, '')
                : null,
        ]);

        $notifier->notify($recipient, 'contact message', $data);

        return to_route('contact')->with('status', 'Thank you. Your message has been received.');
    }
}
