<?php

use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $initialTariffs = [
            ['name' => 'Bronze', 't_floor' => 1, 't_ceiling' => 50, 'amount' => 5],
            ['name' => 'Silver', 't_floor' => 51, 't_ceiling' => 100, 'amount' => 7],
            ['name' => 'Gold', 't_floor' => 101, 't_ceiling' => 500, 'amount' => 15],
            ['name' => 'Platinum', 't_floor' => 501, 't_ceiling' => 100000, 'amount' => 20],
        ];

        \DB::table('tariffs')->truncate();
        \DB::table('tariffs')->insert($initialTariffs);
    }
}
