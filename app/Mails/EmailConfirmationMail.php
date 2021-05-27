<?php

namespace App\Mails;

use App\Services\SettingService;
use Illuminate\Support\Arr;

class EmailConfirmationMail extends BaseMail
{
    public function __construct($to, array $data)
    {
        parent::__construct(
            $to,
            $data,
            'Email confirmation for PFS Cloud App',
            'emails.email_confirmation'
        );

        $adminEmail = app(SettingService::class)->get('admin_email');

        if (Arr::get($adminEmail, 'email')) {
            $this->cc($adminEmail['email']);
        }
    }
}
