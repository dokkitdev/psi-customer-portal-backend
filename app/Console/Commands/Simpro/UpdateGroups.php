<?php

namespace App\Console\Commands\Simpro;

use App\ApiClients\SimproApiClient;
use App\Services\GroupService;
use App\Services\SimproSiteService;
use Exception;
use Illuminate\Console\Command;

class UpdateGroups extends Command
{
    protected $signature = 'simpro:update-groups';

    protected $description = 'Update Groups';

    protected SimproSiteService $simproSiteService;
    protected SimproApiClient $simproClient;
    protected GroupService $groupService;

    public function handle()
    {
        $this->groupService = app(GroupService::class);
        $this->simproClient = app(SimproApiClient::class);
        $this->simproSiteService = app(SimproSiteService::class);

        $groups = $this->groupService->get();

        foreach ($groups as $group) {
            try {
                $this->simproSiteService->attachSites($group['simpro_customer_id'], $group);
            } catch (Exception $e) {
                report($e);
            }
        }

        $this->line('Groups updated');
    }
}
