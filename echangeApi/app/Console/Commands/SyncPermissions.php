<?php

namespace App\Console\Commands;

use App\Constants\Permissions;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;
use ReflectionClass;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:permissions:sync {--truncate : truncate the permission table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all permissions with database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $permissionsClassData = new ReflectionClass(Permissions::class);
        $permissions = array_filter($permissionsClassData->getConstants(), function ($key) {
            return str_starts_with($key, 'PERM_');
        }, ARRAY_FILTER_USE_KEY);

        foreach ($permissions as $key => $permission) {
            Permission::updateOrCreate([
                'name' => $permission,
                'key' => $key,
                'scope' => Permission::getPermissionScope($permission),
            ]);
        }

        $role = Role::updateOrCreate(['name' => Role::SUPER_ADMIN_ROLE_NAME]);
        $role->givePermissionTo(Permission::all());
        $this->info('Sync completed');

        return self::SUCCESS;
    }
}
