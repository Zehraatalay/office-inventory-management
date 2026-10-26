<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $resources = [
            'role',
            'asset',
            'employee',
            'project',
            'user',
        ];

        $resourceAbilities = [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];

        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($resourceAbilities as $ability) {
                // Role management currently uses six permissions.
                if (
                    $resource === 'role' &&
                    ! in_array($ability, [
                        'view',
                        'view_any',
                        'create',
                        'update',
                        'delete',
                        'delete_any',
                    ], true)
                ) {
                    continue;
                }

                $permissions[] = "{$ability}_{$resource}";
            }
        }

        $permissions = array_merge($permissions, [
            'page_ZimmetDetay',
            'widget_BarChart',
            'widget_BubbleChart',
            'widget_DoughnutChart',
            'widget_LineChart',
            'widget_PieChart',
            'widget_StatsOverview',
        ]);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdmin = Role::findOrCreate('super_admin', 'web');
        $intern = Role::findOrCreate('Stajyer', 'web');

        $superAdmin->syncPermissions($permissions);

        $intern->syncPermissions([
            'view_any_asset',
            'view_asset',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}