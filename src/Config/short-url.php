<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Routing
    |--------------------------------------------------------------------------
    |
    | If the default route is disabled, this prevents any visitors
    | from using the default route that comes with this library.
    | This allows you to define your own route that points to
    | the controller at:

    | AshAllenDesign\ShortURL\Controllers\ShortURLController
    |
    */
    'disable_default_route' => false,

    /*
    |--------------------------------------------------------------------------
    | URL Length
    |--------------------------------------------------------------------------
    |
    | The character length of the shortened URL.
    | e.g. - Using a length of 3 would result in yourdomain.com/XXX
    |      - Using a length of 5 would result in yourdomain.com/XXXXX
    |
    */
    'key_length' => 5,

    /*
    |--------------------------------------------------------------------------
    | Tracking
    |--------------------------------------------------------------------------
    |
    | Define which fields are recorded if a shortened URL has
    | tracking enabled. Also define whether if tracking
    | is enabled by default.
    |
    */
    'tracking'   => [
        'default_enabled' => true,

        'fields' => [
            'ip_address'               => true,
            'operating_system'         => true,
            'operating_system_version' => true,
            'browser'                  => true,
            'browser_version'          => true,
        ],
    ],
];
