<?php

namespace App\Console\Commands;

use App\Constants\Options;
use App\Models\Option;
use App\Models\Organization;
use Illuminate\Console\Command;

class SyncOptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:options:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all options with database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $created = 0;
        $updated = 0;
        foreach (Organization::all() as $organization) {
            foreach (Options::OPTIONS_MAP as $moduleName => $moduleFields) {
                foreach ($moduleFields as $fieldName => $fieldOptions) {
                    $option = Option::where([
                        'organization_id' => $organization->id,
                        'name' => $fieldName,
                    ])->first();

                    if ($option == null) {
                        $option = new Option();
                        $option->organization_id = $organization->id;
                        $option->module = $moduleName;
                        $option->name = $fieldName;
                        $option->value_type = $fieldOptions['type'];
                        $option->value = $fieldOptions['default'];
                        $option->data = isset($fieldOptions['data']) ? $fieldOptions['data'] : null;
                        $option->save();
                        $created++;
                    } else {
                        var_dump(isset($fieldOptions['data']) ? $fieldOptions['data'] : null);
                        $option->value_type = $fieldOptions['type'];
                        $option->module = $moduleName;
                        $option->data = isset($fieldOptions['data']) ? $fieldOptions['data'] : null;
                        $option->save();
                        $updated++;
                    }
                }
            }
        }

        $this->info('Sync success');
        $this->info("\t+ $created options created");
        $this->info("\t+ $updated options updated");

        return self::SUCCESS;
    }
}
