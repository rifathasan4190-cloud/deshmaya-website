<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscardHoneypotSubmission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('contact.submit', 'request-quote.submit') && filled($request->input('website'))) {
            $route = $request->routeIs('contact.submit') ? 'contact' : 'request-quote';

            return to_route($route)->with('status', 'Thank you. Your submission has been received.');
        }

        return $next($request);
    }
}
