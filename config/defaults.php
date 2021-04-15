<?php

return [
    'items_per_page' => 10,
    'permitted_media_types' => ['jpg', 'jpeg', 'bmp', 'png', 'pdf'],
    'invoice_permissions' => ['View', 'No Access'],
    'quote_permissions' => ['View', 'Edit', 'No Access'],
    'site_custom_field_ids' => [22, 32, 25, 16, 76],
    'default_employee' => 660,

    /*
    |--------------------------------------------------------------------------
    | Password hash lifetime, hours
    |--------------------------------------------------------------------------
    |
    | Here you can set how often "set_password_hash" field of "users" table will be clearing.
    |
    */

    'password_hash_lifetime' => env('PASSWORD_HASH_LIFETIME', 1)
];
