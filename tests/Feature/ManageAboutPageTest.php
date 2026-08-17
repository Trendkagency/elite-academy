<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Filament\Pages\ManageAboutPage;
use App\Models\AdminProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageAboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_about_page_cms_settings(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.about@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $admin->id]);

        $this->actingAs($admin);

        Livewire::test(ManageAboutPage::class)
            ->fillForm([
                'about_hero_badge' => 'DYNAMIC ABOUT BADGE',
                'about_hero_title' => 'Dynamic About Title',
                'about_stat_students' => '99,999+',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('DYNAMIC ABOUT BADGE', SiteSetting::get('about_hero_badge'));
        $this->assertEquals('99,999+', SiteSetting::get('about_stat_students'));

        $response = $this->get('/about');
        $response->assertStatus(200)
            ->assertSee('DYNAMIC ABOUT BADGE')
            ->assertSee('99,999+');
    }
}
