<?php

namespace Database\Seeders;

use App\Constants\Permissions;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationLocationStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organization1 = Organization::whereEmail('organization1@reseller1.test')->first();

        $location1 = Location::create([
            'name' => 'Location 1 O1 R1',
            'excerpt' => 'location 1 organization 1 reseller 1',
            'organization_id' => $organization1->id,
        ]);
        $location2 = Location::create([
            'name' => 'Location 2 O1 R1',
            'excerpt' => 'location 2 organization 1 reseller 1',
            'organization_id' => $organization1->id,
        ]);

        $locationRole1 = Role::create([
            'name' => 'Role organization 1 location',
            'organization_id' => $organization1->id,
        ]);
        $locationRole1->syncPermissions([
            Permissions::PERM_VIEW_PRODUCTS,
            Permissions::PERM_VIEW_MODULE_PRODUCTS,
            Permissions::PERM_CREATE_PRODUCTS,
            Permissions::PERM_EDIT_PRODUCTS,
            Permissions::PERM_VIEW_ANY_PRODUCTS,
            Permissions::PERM_VIEW_PRODUCTS,
            Permissions::PERM_VIEW_CONTACTS,
            Permissions::PERM_VIEW_MODULE_CONTACTS,
            Permissions::PERM_CREATE_CONTACTS,
            Permissions::PERM_EDIT_CONTACTS,
            Permissions::PERM_VIEW_ANY_CONTACTS,
            Permissions::PERM_VIEW_CONTACTS,
            Permissions::PERM_VIEW_CUSTOMERS,
            Permissions::PERM_VIEW_MODULE_CUSTOMERS,
            Permissions::PERM_CREATE_CUSTOMERS,
            Permissions::PERM_EDIT_CUSTOMERS,
            Permissions::PERM_VIEW_ANY_CUSTOMERS,
            Permissions::PERM_VIEW_CUSTOMERS,
            Permissions::PERM_VIEW_SUPPLIERS,
            Permissions::PERM_VIEW_MODULE_SUPPLIERS,
            Permissions::PERM_CREATE_SUPPLIERS,
            Permissions::PERM_EDIT_SUPPLIERS,
            Permissions::PERM_VIEW_ANY_SUPPLIERS,
            Permissions::PERM_VIEW_SUPPLIERS,
            permissions::PERM_VIEW_MODULE_FILES,
            permissions::PERM_VIEW_ANY_FILES,
            permissions::PERM_VIEW_FILES,
            permissions::PERM_CREATE_FILES,
            permissions::PERM_EDIT_FILES,
            permissions::PERM_DELETE_FILES,
            permissions::PERM_VIEW_MODULE_FOLDERS,
            permissions::PERM_VIEW_ANY_FOLDERS,
            permissions::PERM_VIEW_FOLDERS,
            permissions::PERM_CREATE_FOLDERS,
            permissions::PERM_EDIT_FOLDERS,
            permissions::PERM_DELETE_FOLDERS,
            permissions::PERM_VIEW_APP_DEFAULT,
            permissions::PERM_VIEW_APP_FILE_EXPLORER,
            permissions::PERM_VIEW_APP_CRM,
            permissions::PERM_VIEW_APP_INVENTORY,
        ]);

        $userLocation1 = User::create([
            'active' => true,
            'firstname' => 'User 1',
            'lastname' => 'Location 1 O1 R1',
            'is_staff' => false,
            'email' => 'user1@l1.o1r1',
            'password' => '123456789',
            'profile_image' => env('APP_URL').'/images/admin.jpg',
            'locale' => 'fr',
            'restrict_to_locations' => true,
            'organization_id' => $organization1->id,
        ])
            ->assignRole('Role organization 1 location')
            ->allowedLocations()->sync([$location1->id]);

        $userLocation2 = User::create([
            'active' => true,
            'firstname' => 'User 2',
            'lastname' => 'Location 2 O1 R1',
            'is_staff' => false,
            'email' => 'user2@l2.o1r1',
            'password' => '123456789',
            'profile_image' => env('APP_URL').'/images/admin.jpg',
            'locale' => 'fr',
            'restrict_to_locations' => true,
            'organization_id' => $organization1->id,
        ])
            ->assignRole('Role organization 1 location')
            ->allowedLocations()->sync([$location2->id]);

        $userLocation3 = User::create([
            'active' => true,
            'firstname' => 'User 1',
            'lastname' => 'Location 1 - location 2 O1 R1',
            'is_staff' => false,
            'email' => 'user3@l1l2.o1r1',
            'password' => '123456789',
            'profile_image' => env('APP_URL').'/images/admin.jpg',
            'locale' => 'fr',
            'restrict_to_locations' => true,
            'organization_id' => $organization1->id,
        ])
            ->assignRole('Role organization 1 location')
            ->allowedLocations()->sync([$location1->id, $location2->id]);
    }
}
