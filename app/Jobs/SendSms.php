<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var
     */
    private $to;
    /**
     * @var array
     */
    private $payload;
    /**
     * @var
     */
    private $text;

    /**
     * Create a new job instance.
     *
     * @param $to
     * @param $text
     * @internal param array $payload
     */
    public function __construct($to, $text)
    {
        //
        $this->to = encode_phone_number($to);
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->send_message();
    }

    public function build_message_params()
    {
        $gateway1 = [
            'apiClientID' => '36',
            'key' => 'wyDjxWr9A1SX850KtMn',
            'secret' => 'qpZkwRCm1d10784',
            'txtMessage' => $this->text,
            'MSISDN' => $this->to,
            "shortCode" => "PARTYPEOPLE"
        ];

        $gateway2 = [
            'MESSAGE' => $this->text,
            'MSISDN' => $this->to,
            "SOURCE" => "PARTYPEOPLE"
        ];
        return $gateway2;
    }

    public function send_message()
    {
        $url  =  '197.248.4.234/send_bulk_sms';
        $params  = $this->build_message_params();

        $response = \Httpful\Request::post($url,http_build_query($params))
            ->sendsType(\Httpful\Mime::FORM)->send();

        if (!$response->code == 200 || !$response->body) {
            //stuff failed
            return false;
        }

        return $response->body;
    }
}
