<?php

use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            [
                'owner_id' => 1,
                'owner_type' => \App\Models\Account::AC_SUSPENSE,
                'name' => 'General Ledger Account',
                'credit' => 0,
                'debit' => 0
            ],
            [
                'owner_id' => 2,
                'owner_type' => \App\Models\Account::AC_SUSPENSE,
                'name' => 'Withdrawal Suspense Account',
                'credit' => 0,
                'debit' => 0
            ],
            [
                'owner_id' => 3,
                'owner_type' => \App\Models\Account::AC_SUSPENSE,
                'name' => 'Transaction Fee Suspense Account',
                'credit' => 0,
                'debit' => 0
            ]
        ];

        \DB::table('accounts')->where(['owner_type' => 'Suspense'])->delete();
        \DB::table('accounts')->insert($accounts);
    }
}
