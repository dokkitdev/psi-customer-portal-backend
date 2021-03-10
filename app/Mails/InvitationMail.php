<?php

namespace App\Mails;

class InvitationMail extends BaseMail
{
    public function __construct($to, array $data)
    {
        parent::__construct(
            $to,
            $data,
            'Invitation to the PFS Cloud App',
            'emails.invitation'
        );
    }
}
