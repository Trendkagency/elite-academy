<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Permissions\PermissionsRegistry;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::values();
        $allPermissions = PermissionsRegistry::all();

        $matrix = [];
        foreach ($roles as $role) {
            $matrix[$role] = PermissionsRegistry::defaultPermissionsForRole($role);
        }

        // Store permissions matrix in cache / system state for rapid resolution
        cache()->forever('system_permissions_matrix', $matrix);
        cache()->forever('system_permissions_list', $allPermissions);
    }
}
