<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Filament\Pages\ManageGeneralSettings;
use App\Models\AdminProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('demo.enabled', true);

        $this->adminUser = User::create([
            'name' => 'Demo Administrator',
            'email' => 'admin@trendk.com',
            'password' => bcrypt('Demo@12345'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);
    }

    public function test_admin_can_access_general_settings_page(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/admin/manage-general-settings');
        $response->assertSuccessful();
    }

    public function test_brand_helper_prioritizes_database_over_demo_config(): void
    {
        Config::set('demo.branding.app_name', 'Demo Config Academy');

        // Without DB setting, it falls back to demo config
        $this->assertEquals('Demo Config Academy', brand('app_name'));

        // When saved in SiteSetting, DB value takes immediate precedence
        SiteSetting::set('app_name_en', 'Database Custom Academy', 'branding');
        SiteSetting::set('app_name_ar', 'أكاديمية مخصصة من الإعدادات', 'branding');

        $this->assertEquals('Database Custom Academy', brand('app_name', null, 'en'));
        $this->assertEquals('أكاديمية مخصصة من الإعدادات', brand('app_name', null, 'ar'));
    }

    public function test_brand_logo_helper_resolves_database_logos_with_storage_url(): void
    {
        Config::set('demo.branding.logo', 'demo/logo.svg');
        Config::set('demo.branding.logo_dark', 'demo/logo-dark.svg');
        Config::set('demo.branding.favicon', 'demo/favicon.ico');

        // Initially returns demo logos
        $this->assertStringContainsString('demo/logo.svg', brand_logo('light'));
        $this->assertStringContainsString('demo/logo-dark.svg', brand_logo('dark'));
        $this->assertStringContainsString('demo/favicon.ico', brand_logo('favicon'));

        // When uploaded and stored in SiteSetting
        SiteSetting::set('site_logo_light', 'branding/test-light-logo.png', 'branding');
        SiteSetting::set('site_logo_dark', 'branding/test-dark-logo.png', 'branding');
        SiteSetting::set('site_favicon', 'branding/custom-favicon.ico', 'branding');

        $this->assertStringContainsString('storage/branding/test-light-logo.png', brand_logo('light'));
        $this->assertStringContainsString('storage/branding/test-dark-logo.png', brand_logo('dark'));
        $this->assertStringContainsString('storage/branding/custom-favicon.ico', brand_logo('favicon'));
    }

    public function test_currency_helpers_synchronize_with_database_settings(): void
    {
        Config::set('demo.branding.currency.code', 'EGP');

        SiteSetting::set('currency_code', 'SAR', 'billing');
        SiteSetting::set('currency_symbol_ar', 'ر.س', 'billing');
        SiteSetting::set('currency_symbol_en', 'SAR', 'billing');
        SiteSetting::set('currency_position', 'before', 'billing');

        $this->assertEquals('SAR', app_currency());
        $this->assertEquals('SAR', currency_symbol('en'));
        $this->assertEquals('ر.س', currency_symbol('ar'));

        $this->assertEquals('SAR 1,500', format_currency(1500, 'en'));
        $this->assertEquals('ر.س 1,500', format_currency(1500, 'ar'));
    }

    public function test_brand_color_helper_resolves_database_colors(): void
    {
        Config::set('demo.branding.primary_color', '#0d9488');

        // Without DB setting, it falls back to demo config
        $this->assertEquals('#0d9488', brand_color('primary'));

        // When saved in SiteSetting
        SiteSetting::set('theme_primary_color', '#6366f1', 'theme');
        $this->assertEquals('#6366f1', brand_color('primary'));
    }

    public function test_settings_page_saves_and_syncs_form_state(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(ManageGeneralSettings::class)
            ->fillForm([
                'app_name_en' => 'Elite STEM Academy',
                'app_name_ar' => 'أكاديمية إيليت ستيم',
                'currency_code' => 'USD',
                'currency_symbol_en' => '$',
                'currency_symbol_ar' => '$',
                'theme_primary_color' => '#0ea5e9',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Elite STEM Academy', SiteSetting::get('app_name_en'));
        $this->assertEquals('أكاديمية إيليت ستيم', SiteSetting::get('app_name_ar'));
        $this->assertEquals('USD', SiteSetting::get('currency_code'));
        $this->assertEquals('#0ea5e9', SiteSetting::get('theme_primary_color'));

        $this->assertEquals('Elite STEM Academy', brand('app_name', null, 'en'));
        $this->assertEquals('أكاديمية إيليت ستيم', brand('app_name', null, 'ar'));
        $this->assertEquals('USD', app_currency());
        $this->assertEquals('#0ea5e9', brand_color('primary'));
    }
}
