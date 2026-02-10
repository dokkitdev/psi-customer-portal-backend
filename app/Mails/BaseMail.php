<?php

namespace App\Mails;

use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($to, array $data, $subject, $view)
    {
        $this->to($to);
        $this->data = $data;
        $this->subject = $subject;
        $this->view = $view;

        $adminEmail = app(SettingService::class)->get('admin_email');

        if (Arr::get($adminEmail, 'email')) {
            $this->from($adminEmail['email']);
        }
    }

    public function build()
    {
        return $this
            ->view($this->view)
            ->subject($this->subject)
            ->with($this->data);
    }

    public function getData()
    {
        return $this->data;
    }
}