<?php

return [
    'items_per_page' => 10,
    'permitted_media_types' => ['jpg', 'jpeg', 'bmp', 'png', 'pdf'],
    'max_media_size' => 102400,
    'invoice_permissions' => ['View', 'No Access'],
    'quote_permissions' => ['View', 'Edit', 'No Access'],
    'site_custom_field_ids' => [22, 32, 25, 16, 76],
    'default_employee' => 660,
    'quote_status' => 135,
    'job_status' => 100,
    'quote_job_custom_field_id' => 4,
    'job_name_tags' => [8, 127, 132],

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
