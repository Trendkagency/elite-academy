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

    public function test_admin_can_update_owner_payment_whatsapp_number(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.payment@elite.edu',
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
                'owner_whatsapp' => '+201299988877',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('+201299988877', SiteSetting::get('owner_whatsapp'));

        $parentUser = User::create([
            'name' => 'Test Parent WhatsApp',
            'email' => 'parentwa@test.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        \App\Models\ParentProfile::create(['user_id' => $parentUser->id]);

        $response = $this->actingAs($parentUser)->get('/parent-portal');
        $response->assertStatus(200);
        $response->assertSee('201299988877');
    }
}
