<?php

namespace Tests\Feature;

use App\Filament\Admin\Pages\BrandColors;
use App\Filament\Admin\Pages\BusinessInfo;
use App\Filament\Admin\Pages\LoaderSettings;
use App\Filament\Admin\Pages\SeoSettings;
use App\Models\User;
use App\Services\SettingService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\SettingsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_color_change_reaches_the_page(): void
    {
        $this->seed(SettingsSeeder::class);
        app(SettingService::class)->set('brand.primary', '#112233', 'color');

        $this->get('/')->assertOk()->assertSee('--brand-primary:#112233', false);
    }

    public function test_invalid_color_is_not_printed(): void
    {
        app(SettingService::class)->set('brand.primary', 'red;}</style><script>', 'color');

        $this->get('/')->assertOk()->assertDontSee('--brand-primary:red', false);
    }

    public function test_business_info_updates_footer(): void
    {
        app(SettingService::class)->set('contact.email_primary', 'hello@example.com');

        $this->get('/')->assertSee('hello@example.com');
    }

    public function test_only_admin_users_can_access_the_filament_panel(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin)->get('/admin')->assertOk();

        $user = User::query()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => 'test-password',
            'is_admin' => false,
        ]);
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_seeder_uses_configured_credentials(): void
    {
        config([
            'admin.seed_name' => 'Site Administrator',
            'admin.seed_email' => 'admin@example.com',
            'admin.seed_password' => 'test-secret-password',
        ]);

        $this->seed(AdminUserSeeder::class);

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame('Site Administrator', $admin->name);
        $this->assertTrue($admin->is_admin);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(password_verify('test-secret-password', $admin->password));
    }

    public function test_admin_settings_pages_render_for_admins(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get('/admin/brand-colors')
            ->assertOk()
            ->assertSee('Primary')
            ->assertSee('Reset to defaults');

        $this->get('/admin/business-info')->assertOk()->assertSee('Business settings');
        $this->get('/admin/loader-settings')->assertOk()->assertSee('Page loader');
        $this->get('/admin/seo-settings')->assertOk()->assertSee('Search and social defaults');
    }

    public function test_brand_color_admin_form_saves_through_the_settings_service(): void
    {
        $admin = $this->createAdmin();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(BrandColors::class)
            ->set('data.primary', '#112233')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('#112233', app(SettingService::class)->get('brand.primary'));
    }

    public function test_business_form_saves_fields_to_their_setting_groups(): void
    {
        $admin = $this->createAdmin();
        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(BusinessInfo::class)
            ->set('data.phone_primary', '+880123456789')
            ->set('data.facebook', 'https://facebook.com/deshmaya')
            ->set('data.notification_email', 'admin@example.com')
            ->call('save')
            ->assertHasNoErrors();

        $settings = app(SettingService::class);

        $this->assertSame('+880123456789', $settings->get('contact.phone_primary'));
        $this->assertSame('https://facebook.com/deshmaya', $settings->get('social.facebook'));
        $this->assertSame('admin@example.com', $settings->get('mail.notification_email'));
    }

    public function test_loader_and_seo_forms_save_their_values(): void
    {
        $admin = $this->createAdmin();
        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(LoaderSettings::class)
            ->set('data.enabled', false)
            ->set('data.min_ms', '900')
            ->set('data.max_ms', '4000')
            ->call('save')
            ->assertHasNoErrors();

        Livewire::actingAs($admin)
            ->test(SeoSettings::class)
            ->set('data.default_title', 'Deshmaya Global Produce')
            ->call('save')
            ->assertHasNoErrors();

        $settings = app(SettingService::class);

        $this->assertSame('0', $settings->get('loader.enabled'));
        $this->assertSame('900', $settings->get('loader.min_ms'));
        $this->assertSame('4000', $settings->get('loader.max_ms'));
        $this->assertSame('Deshmaya Global Produce', $settings->get('seo.default_title'));
    }

    private function createAdmin(): User
    {
        $admin = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'test-password',
        ]);

        $admin->forceFill(['is_admin' => true]);
        $admin->save();

        return $admin;
    }
}
