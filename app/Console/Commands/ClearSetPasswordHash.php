<?php

namespace App\Console\Commands;

use App\Services\UserService;

class ClearSetPasswordHash extends TimeoutCommand
{
    protected $signature = 'clear:set-password-hash';

    protected $description = 'Clear set_password_hash in users table';

    public function handle()
    {
        app(UserService::class)->clearSetPasswordHash();

        $this->line('Set password hash was cleared');
    }
}
