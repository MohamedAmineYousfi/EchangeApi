<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('app:permissions:sync');

        $role = Role::updateOrCreate(['name' => Role::SUPER_ADMIN_ROLE_NAME]);
        $role->givePermissionTo(Permission::all());

        User::create([
            'active' => true,
            'firstname' => env('DEFAULT_ADMIN_FIRSTNAME'),
            'lastname' => env('DEFAULT_ADMIN_LASTNAME'),
            'is_staff' => true,
            'email' => env('DEFAULT_ADMIN_EMAIL'),
            'password' => env('DEFAULT_ADMIN_PASSWORD'),
            'profile_image' => env('APP_URL').'/images/admin.jpg',
            'locale' => 'fr',
        ])->assignRole(Role::SUPER_ADMIN_ROLE_NAME);
    }
}
