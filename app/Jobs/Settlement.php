<?php

namespace App\Jobs;

use App\AccountableTrait;
use App\Models\Account;
use App\Models\BankAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Settlement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var \App\Models\Settlement
     */
    private $settlement;
    /**
     * @var AccountableTrait
     */
    private $accountable;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Settlement $settlement
     */
    public function __construct(\App\Models\Settlement $settlement)
    {
        //
        $this->settlement = $settlement;
        $this->accountable = $settlement->account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $settlement  =  $this->settlement;
        $accountable  =  $this->accountable;

        \DB::transaction(function () use(&$settlement, &$accountable) {
            $this->remove_money_from_account($accountable, $settlement);
            $params  = $this->prepare_params($settlement);
            $result = $this->send_pesaflow_request($params);
            $this->process_settlement_result($result, $settlement);
        });
    }

    /**
     * @param AccountableTrait $accountableTrait
     * @param \App\Models\Settlement $settlement
     * @return $this
     */
    public function remove_money_from_account($accountableTrait, \App\Models\Settlement $settlement)
    {
        \DB::beginTransaction();
        //debit gla and credit withdrawal suspense account
        $gla = Account::get_general_ledger_account();
        Account::debit($gla->owner_id, $gla->owner_type, $settlement->amount, $settlement->id, $settlement->notes);

        //transfer from holder account to withdrawal suspense account
        $account = $accountableTrait->getAccount();
        $suspense = Account::get_withdrawal_suspense_account();

        $transfer_payload = [
            'debit' => [
                'owner_id' => $account->owner_id,
                'owner_type' => $account->owner_type
            ],
            'credit' => [
                'owner_id' => $suspense->owner_id,
                'owner_type' => $suspense->owner_type
            ]
        ];

        Account::transfer($transfer_payload, $settlement->amount, $settlement->id, $settlement->notes);
        \DB::commit();

        return $this;
    }

    /**
     * @param \App\Models\Settlement $settlement
     * @return array
     */
    public function prepare_params(\App\Models\Settlement $settlement)
    {
        $paybill  = $settlement->get_paybill();
        $pesaflow_secret = settings('pesaflow_secret');
        $key  = base64_encode("{$settlement->id}{$settlement->amount}{$paybill}{$pesaflow_secret}");

        return [
            "client_id" => settings('pesaflow_client_id'),
            "conversation_id" => $settlement->id,
            "type" => "B2B",
            "amount" => $settlement->amount,
            "bank" => $settlement->bank,
            "shortcode" => $paybill,
            "branch" => 'Branch',
            "account_name" =>  $settlement->account_name,
            "account_number" => $settlement->account_no,
            "key" => $key
        ];
    }

    /**
     * @param array $params
     * @return array|bool|int|object|string
     */
    public function send_pesaflow_request(array $params)
    {
        if (\App::environment('prod','production')){
            \Log::info("Create Settlement Call: ", [ "payload" => $params]);

            $url  =  settings('pesaflow_api_url');
            $response = \Httpful\Request::post($url,http_build_query($params))
                ->sendsType(\Httpful\Mime::FORM)->send();

            \Log::info("Create Settlement Response");
            \Log::info($response->body);

            if (!$response->code == 200 || !$response->body) {
                //stuff failed
                return false;
            }

            return $response->body;
        }

        return rand(1,9999);
    }

    /**
     * @param $result
     * @param \App\Models\Settlement $settlement
     * @return Settlement|bool
     */
    public function process_settlement_result($result,  \App\Models\Settlement $settlement)
    {
        $result = intval($result);
        if ($result)
         return  $settlement->update(['status' => 'processed', 'conversation_id' => $result]);

        return $this->process_failed_settlement($settlement);
    }

    /**
     * @param \App\Models\Settlement $settlement
     * @return $this
     */
    public function process_failed_settlement(\App\Models\Settlement $settlement)
    {
        //debit the withdrawal suspense account and credit back the owner account
        $account = $settlement->account->getAccount();
        $suspense = Account::get_withdrawal_suspense_account();

        $transfer_payload = [
            'debit' => [
                'owner_id' => $suspense->owner_id,
                'owner_type' => $suspense->owner_type
            ],
            'credit' => [
                'owner_id' => $account->owner_id,
                'owner_type' => $account->owner_type
            ]
        ];

        \DB::beginTransaction();
        Account::transfer($transfer_payload, $settlement->amount, $settlement->id, "Failed settlement for: ".$settlement->notes);

        //credit the the gla with money back
        $gla = Account::get_generate_ledger_account();
        Account::credit($gla->owner_id, $gla->owner_type, $settlement->amount, $settlement->id, "Failed settlement for: ".$settlement->notes);
        \DB::commit();

        return $this;
    }

}
