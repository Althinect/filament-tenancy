<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionClasses = File::allFiles(app_path('Permissions'));

        $permissions = [];

        foreach ($permissionClasses as $permissionClass) {
            $permissionClassName = 'App\\Permissions\\'.$permissionClass->getBasename('.php');
            $permissionClassReflection = new \ReflectionClass($permissionClassName);

            if ($permissionClassReflection->isEnum()) {
                if ($permissionClassReflection->hasMethod('all')) {
                    $permissions = array_merge($permissions, $permissionClassName::all());
                }
            }
        }

        $guardNames = ['web', 'api'];

        foreach ($permissions as $permission) {
            foreach ($guardNames as $guardName) {
                //firstOrCreate will create the role if it does not exist
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guardName,
                ]);
            }
        }
    }
}
