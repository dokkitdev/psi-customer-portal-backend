<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class TimeoutCommand extends Command
{
    protected const TIMEOUT_EXIT_CODE = 1;

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->registerTimeoutHandler();

        return parent::run($input, $output);
    }

    protected function registerTimeoutHandler()
    {
        $timeout = config('artisan.timeout_seconds');

        if ($timeout > 0) {
            pcntl_signal(SIGALRM, function () {
                $this->kill(self::TIMEOUT_EXIT_CODE);
            });

            pcntl_alarm($timeout);
        }
    }
}
