<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hospital EMR Integration
    |--------------------------------------------------------------------------
    |
    | Credentials for the bidirectional integration with the external doctor /
    | medical record system. Secrets must be provided through the environment,
    | never committed to source control.
    |
    | OUTBOUND_API_KEY is the credential THIS system uses when calling them.
    | INBOUND_API_KEY is the credential THEY use when calling this system
    | (generated through `php artisan api-key:create` or POST /api/v1/api-keys).
    |
    */

    'hospital_emr' => [
        'base_url' => env('HOSPITAL_EMR_BASE_URL'),
        'outbound_api_key' => env('HOSPITAL_EMR_OUTBOUND_API_KEY'),
        'inbound_api_key' => env('HOSPITAL_EMR_INBOUND_API_KEY'),

        // Used to sign webhooks we send.
        'webhook_signing_secret' => env('HOSPITAL_EMR_WEBHOOK_SIGNING_SECRET'),

        // Used to verify webhooks we receive.
        'webhook_verify_secret' => env('HOSPITAL_EMR_WEBHOOK_VERIFY_SECRET'),

        // Their endpoint that receives our signed webhooks.
        'webhook_url' => env('HOSPITAL_EMR_WEBHOOK_URL'),
    ],

];
