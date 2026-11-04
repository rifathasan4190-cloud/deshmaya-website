<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\DiscardHoneypotSubmission;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

Route::get('/', HomeController::class)->name('home');
Route::view('/about-us', 'pages.about')->name('about');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/category/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::view('/certification', 'pages.certification')->name('certification');
Route::view('/how-we-export', 'pages.how-we-export')->name('how-we-export');
Route::view('/export-markets', 'pages.export-markets')->name('export-markets');
Route::view('/quality-packaging', 'pages.quality-packaging')->name('quality-packaging');
Route::get('/contact-us', [ContactMessageController::class, 'create'])->name('contact');
Route::post('/contact-us', [ContactMessageController::class, 'store'])
    ->middleware(['throttle:forms', DiscardHoneypotSubmission::class])
    ->name('contact.submit');
Route::get('/request-a-quote', [QuoteRequestController::class, 'create'])->name('request-quote');
Route::post('/request-a-quote', [QuoteRequestController::class, 'store'])
    ->middleware(['throttle:forms', DiscardHoneypotSubmission::class])
    ->name('request-quote.submit');
