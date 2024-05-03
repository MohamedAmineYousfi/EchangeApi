<?php

namespace App\Console\Commands;

use App\Constants\Permissions;
use Illuminate\Console\Command;
use ReflectionClass;

class DumpPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:permissions:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump permissions to javascript object';

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

        $output = $this->choice(
            'Speccify the output of the dump',
            ['json', 'jsobject']
        );

        $this->info('Permissions : ');

        if ($output == 'json') {
            $this->info(json_encode($permissions));
        } elseif ($output = 'jsobject') {
            $this->info(preg_replace('/"([^"]+)"\s*:\s*/', '$1:', json_encode($permissions)));
        }

        return self::SUCCESS;
    }
}
