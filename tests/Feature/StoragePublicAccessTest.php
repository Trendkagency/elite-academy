<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoragePublicAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_htaccess_allows_public_storage_urls_while_blocking_internal_directories(): void
    {
        $htaccess = file_get_contents(base_path('.htaccess'));

        $this->assertNotFalse($htaccess);
        $this->assertStringNotContainsString(
            'RewriteRule ^(storage|bootstrap/cache)/ - [F,L,NC]',
            $htaccess,
            'Root .htaccess must not indiscriminately block /storage/* with 403 Forbidden.'
        );
        $this->assertStringContainsString('RewriteRule ^storage/?$ - [F,L,NC]', $htaccess);
        $this->assertStringContainsString('RewriteRule ^storage/(app|framework|logs|debugbar|clockwork)/ - [F,L,NC]', $htaccess);
        $this->assertStringContainsString('RewriteRule ^bootstrap/cache/ - [F,L,NC]', $htaccess);
    }

    public function test_storage_fallback_serves_public_and_migrates_private_avatars_safely(): void
    {
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $publicName = 'avatars/test-public-'.Str::random(8).'.png';
        $privateName = 'avatars/test-private-'.Str::random(8).'.png';

        $publicFull = storage_path('app/public/'.$publicName);
        $privateFull = storage_path('app/private/'.$privateName);
        $migratedFull = storage_path('app/public/'.$privateName);

        try {
            @mkdir(dirname($publicFull), 0755, true);
            file_put_contents($publicFull, $pngBytes);

            $this->get('/storage/'.$publicName)
                ->assertStatus(200);

            @mkdir(dirname($privateFull), 0755, true);
            file_put_contents($privateFull, $pngBytes);

            $this->get('/storage/'.$privateName)
                ->assertStatus(200);

            $this->assertFileExists($migratedFull);

            $this->get('/storage/avatars/.gitignore')->assertStatus(404);
            $this->get('/storage/avatars/nonexistent-'.Str::random(8).'.png')->assertStatus(404);
        } finally {
            @unlink($publicFull);
            @unlink($privateFull);
            @unlink($migratedFull);
        }
    }

    public function test_student_profile_avatar_url_resolves_public_private_and_safe_fallback(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $user = User::factory()->create(['name' => 'Mariam Avatar', 'status' => AccountStatus::APPROVED]);
        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'avatar' => 'avatars/missing.png',
        ]);

        // Missing file on both public & local disks falls back cleanly to ui-avatars
        $this->assertStringContainsString('ui-avatars.com', $profile->avatar_url);

        Storage::disk('public')->put('avatars/in-public.png', 'img');
        $profile->avatar = 'avatars/in-public.png';
        $this->assertStringContainsString('/storage/avatars/in-public.png', $profile->avatar_url);

        Storage::disk('local')->put('avatars/in-private.png', 'img');
        $profile->avatar = 'avatars/in-private.png';
        $this->assertStringContainsString('/storage/avatars/in-private.png', $profile->avatar_url);
    }
}
