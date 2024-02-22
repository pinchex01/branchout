<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //truncate settings table
        \DB::table('settings')->truncate();

        $config = [
            ["key"=>'pesaflow_client_id',"value"=>2],
            ["key"=>'eresident_callback_url',"value"=>'https://eresident.co.ke/account/invoices'],
            ["key"=>'eresident_ipn_url',"value"=>"https://eresident.co.ke:666/payment"],
            ["key"=>'pesaflow_currency',"value"=>"KES"],
            ["key"=>'settlement_transaction_fee',"value"=>"0"],
            ["key"=>'pesaflow_service_id',"value"=>"212"],
            ["key"=>'pesaflow_api_url',"value"=>"197.248.7.61:666/funds_transfer"],
            ["key"=>'pesaflow_key',"value"=>"xxx"],
            ["key"=>'pesaflow_secret',"value"=>"xxx"],
            ["key"=>'withdrawal_suspense_account',"value"=>"2"],
            ["key"=>'general_ledger_account',"value"=>"1"],
            ["key"=>'transaction_fee_account',"value"=>"3"],
            ["key"=>'daemon_max_retries',"value"=>"5"],
            ["key"=>'send_bulk_sms_api',"value"=>"197.248.4.234/send_bulk_sms"],
            ["key"=>'pesaflow_iframe_api',"value"=>"197.248.7.61/PaymentAPI/iframev244444.php"],
            ["key"=>'commission',"value"=>"10"],
            ["key"=>'minimum_rent',"value"=>"100"],
            ["key"=>'settlement_schedule',"value"=>"daily"],
            ["key"=>'tax',"value"=>"0"],
            ["key"=>'tax_account',"value"=>""],
            ['key'=>'sms_gateway','value'=>'1'],
            ['key'=>'overdue_day','value'=>'5'],
            ['key'=>'site_name','value'=>'eResident'],
            ['key'=>'site_url','value'=>'https:://eresident.co.ke'],
            ['key'=>'meta_title','value'=>'eResident'],
            ['key'=>'site_description','value'=>'Collect and pay rent online'],
            ['key'=>'site_keywords','value'=>'online rent payment, pay rent online, property management, landlord, tenant'],
            ['key'=>'site_author','value'=>'eResident Africa'],
            ['key'=>'pesaflow_iframe_url','value'=>'https://pesaflow.ecitizen.go.ke/PaymentAPI/iframev2.1.php'],
            ['key'=>'min_withdrawable','value'=>'100'],
        ];

        \DB::table('settings')->insert($config);
    }
}
