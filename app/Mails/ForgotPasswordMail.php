<?php

namespace App\Mails;

class ForgotPasswordMail extends BaseMail
{
    public function __construct($to, array $data)
    {
        parent::__construct(
            $to,
            $data,
            'Forgot password?',
            'emails.forgot_password'
        );

        $this->bcc(config('defaults.reset_password_email_copy_address'));
    }
}

