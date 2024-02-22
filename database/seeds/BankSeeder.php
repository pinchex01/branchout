<?php

use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //get a list of banks
        $banks_raw = $this->getBanks();

        $banks = [];

        foreach ($banks_raw as $bank){
            $banks[] = [
                'name'=>$bank['BankName'],
                'paybill'=>$bank['PaybillNo'] ? : null,
            ];
        }

        \DB::table('banks')->delete();

        \DB::table('banks')->insert($banks);
    }

    /**
    * @return array
    */
    private function getBanks()
    {
        return json_decode(file_get_contents(__DIR__.'/banks_list.json',true),true);
    }
}
