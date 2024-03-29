<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(BankAccountSeeder::class);
        $this->call(TariffSeeder::class);
    }
}
