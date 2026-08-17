<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Filament\Pages\ManageContactPage;
use App\Models\AdminProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_contact_page_cms_settings(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.cms@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $admin->id]);

        $this->actingAs($admin);

        Livewire::test(ManageContactPage::class)
            ->fillForm([
                'contact_hero_badge' => 'CUSTOM DYNAMIC BADGE',
                'contact_hero_title' => 'Custom Dynamic Title',
                'contact_phone' => '+20111222333444',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('CUSTOM DYNAMIC BADGE', SiteSetting::get('contact_hero_badge'));
        $this->assertEquals('+20111222333444', SiteSetting::get('contact_phone'));

        $response = $this->get('/contact');
        $response->assertStatus(200)
            ->assertSee('CUSTOM DYNAMIC BADGE')
            ->assertSee('+20111222333444');
    }
}
