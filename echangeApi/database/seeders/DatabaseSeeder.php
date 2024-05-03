<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        // Truncate all tables
        $tables = DB::select('SHOW TABLES');
        $column_name = 'Tables_in_'.DB::connection()->getDatabaseName();
        foreach ($tables as $table) {
            if ($table->$column_name !== 'migrations') {
                DB::table($table->$column_name)->truncate();
            }
        }
        Schema::disableForeignKeyConstraints();

        $this->call(UsersSeeder::class);
        $this->call(Reseller1StructureSeeder::class);
        $this->call(Reseller2StructureSeeder::class);
        $this->call(OrganizationLocationStructureSeeder::class);
    }
}
