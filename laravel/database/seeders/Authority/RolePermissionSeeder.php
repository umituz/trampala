<?php

namespace Database\Seeders\Authority;

use App\Enums\Authority\PermissionEnum;
use App\Models\Authority\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->assignPermissionsToRoles();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function assignPermissionsToRoles(): void
    {
        $this->assignAdminPermissions();
        $this->assignUserPermissions();
    }

    private function assignAdminPermissions(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();

        if (!$adminRole) {
            throw new \Exception('Admin role not found. Please run RoleSeeder first.');
        }

        $adminPermissions = PermissionEnum::getAdminPermissions();

        $adminRole->syncPermissions($adminPermissions);
    }

    private function assignUserPermissions(): void
    {
        $userRole = Role::where('name', 'User')->first();

        if (!$userRole) {
            throw new \Exception('User role not found. Please run RoleSeeder first.');
        }

        $userPermissions = PermissionEnum::getUserPermissions();

        $userRole->syncPermissions($userPermissions);
    }
}
