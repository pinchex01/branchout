<?php

/**
 * Laravel pesalflow settings
 */

return [
    /**
     * Indicates the allowed currencies
     */
    'currencies'=>['KES','USD'],

    /**
     * Sets the default currency, can be changed at runtime
     */
    'currency'=>'KES',
    'url'=>env('PESAFLOW_IFRAME_URL'),

    'apiClientId'=>env('PESAFLOW_CLIENT_ID'),
    'apiKey'=>env('PESAFLOW_KEY','key'),
    'apiServiceId'=>env('PESAFLOW_SERVICE_ID'),
    'apiSecret'=>env('PESAFLOW_SECRET','secret'),

    /*
     * IPN endpoint
     */
    'ipn_endpoint' => env('APP_URL').'/payment'

];
