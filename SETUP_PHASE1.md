# Phase 1 Setup (run on your computer, not on cPanel)

Everything in this folder is an overlay for a fresh Laravel project. Same folder names, so copy over the top.

1. Create the project (no starter kit, MySQL):
   composer create-project laravel/laravel deshmaya
2. Copy this overlay into the project folder (merge, do not delete anything).
   Delete the default routes/web.php welcome route if the copy asks, ours replaces it.
3. composer.json -> "autoload": add  "files": ["app/helpers.php"]  then run:  composer dump-autoload
4. bootstrap/providers.php -> add  App\Providers\SiteServiceProvider::class,
5. .env: set DB_*, then  QUEUE_CONNECTION=database  CACHE_STORE=file  SESSION_DRIVER=file  APP_URL=...
6. php artisan migrate
   php artisan db:seed --class=SettingsSeeder
   php artisan storage:link
7. Frontend packages:
   npm i alpinejs lenis
   npm i -D sharp
   package.json scripts -> add  "images": "node scripts/optimize-images.mjs"  and change build to  "build": "npm run images && vite build"
   (The Laravel default already ships Tailwind v4 + Vite. If your app.css still has the old welcome-page styles, ours replaces it.)
8. Put logo and design images in resources/images/ (e.g. logo.svg, hero/home-hero.jpg), then: npm run images
9. Admin panel:
   composer require filament/filament
   php artisan filament:install --panels
   php artisan make:filament-user
10. Run: composer run dev   (or php artisan serve + npm run dev)

## Phase 1 acceptance checks
- [ ] php artisan test passes (3 tests in tests/Feature/SettingsTest.php)
- [ ] Open the site: loader shows then fades; on refresh in the same tab it is skipped
- [ ] In DB change settings brand/primary to another hex, reload: header button and footer color change
- [ ] Set an invalid value (e.g. "red") for a brand color: page still renders with the fallback color
- [ ] Edit contact/phone_primary and contact/email_primary: footer updates; clear them: lines disappear
- [ ] <x-picture src="hero/home-hero.jpg" alt="" :eager="true" /> outputs avif/webp srcset and width/height
- [ ] Emulate mobile in DevTools: menu button works; no horizontal scroll
- [ ] OS "reduce motion" on: no reveal/scroll animation, content visible
- [ ] grep -rE "#[0-9a-fA-F]{3,8}" resources/views returns nothing

## Notes
- Brand colors in the seeder are estimated from the mockup. Give exact values and fonts and we replace them.
- Phase 2 builds the Filament pages that edit these same settings rows.
