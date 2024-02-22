<?php

use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table("bank_accounts")->delete();


        $sys_bank = new \App\Models\BankAccount([
            'name' => 'James Ayugi Otieno',
            'account_no' => '0100346370600',
            'type' => 'bank',
            'is_default' => 1,
            'bank_id' => \App\Models\Bank::where('name','like',"%Kenya Commercial Bank %")->firstOrFail()->id
        ]);
        $sys_bank->owner_id  = '0';
        $sys_bank->owner_type = \App\Models\BankAccount::TYPE_SYSTEM;
        $sys_bank->save();
    }
}
