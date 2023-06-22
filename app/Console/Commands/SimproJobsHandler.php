<?php

namespace App\Console\Commands;

use App\Models\SimproJob;
use App\Services\SimproJobService;
use Exception;
use PHPUnit\Framework\ExpectationFailedException;

class SimproJobsHandler extends TimeoutCommand
{
    protected $signature = 'simpro:handle-jobs';

    protected $description = 'Handle Simpro jobs';

    protected SimproJobService $simproJobService;

    public function handle()
    {
        $this->simproJobService = app(SimproJobService::class);

        $this->simproJobService
            ->getForHandle(1000)
            ->each(function ($job) {
                try {
                    $this->simproJobService->handleJob($job);

                    $this->simproJobService->delete($job->id);
                } catch (Exception $e) {
                    if ((app()->environment() === 'testing') && ($e instanceof ExpectationFailedException)) {
                        throw $e;
                    }
                    report($e);
                    $this->simproJobService->update($job->id, [
                        'handle_status' => SimproJob::HANDLE_STATUS_ERROR,
                        'handle_result' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        ]
                    ]);
                }
            });
    }
}
