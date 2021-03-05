<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\JobWorkOrderRepository;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobWorkOrderRepository $repository
 * @mixin JobWorkOrderRepository
 */
class JobWorkOrderService extends EntityService
{
    protected SimproApiClient $simproClient;

    public function __construct()
    {
        $this->setRepository(JobWorkOrderRepository::class);

        $this->simproClient = app(SimproApiClient::class);
    }

    public function syncBySimpro($companyId, $jobFromSimpro, $jobId)
    {
        $workOrders = $this->getWorkOrdersFromSimpro($companyId, $jobFromSimpro, $jobId);

        $jobWorkOrders = $this->repository->get(['job_id' => $jobId]);

        foreach ($workOrders as $data) {
            $sectionId = $data['section_id'];
            $costCenterId = $data['cost_center_id'];
            $workOrderId = $data['work_order_id'];
            $workOrder = $jobWorkOrders->first(function($item) use ($sectionId, $costCenterId, $workOrderId) {
                return ($item['section_id'] === $sectionId) && ($item['cost_center_id'] === $costCenterId) && ($item['work_order_id'] === $workOrderId);
            });
            if ($workOrder) {
                $this->repository->update($workOrder['id'], $data);
                $jobWorkOrders = $jobWorkOrders->where('id', '!=', $workOrder['id']);
            } else {
                $this->repository->create($data);
            }
        }

        if ($jobWorkOrders->isNotEmpty()) {
            $ids = $jobWorkOrders->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    protected function getWorkOrdersFromSimpro($companyId, $jobFromSimpro, $jobId)
    {
        $sections = Arr::get($jobFromSimpro, 'Sections', []);
        $allWorkOrders = [];

        foreach ($sections as $section) {
            $sectionId = $section['ID'];
            $costCenters = Arr::get($section, 'CostCenters', []);
            foreach ($costCenters as $costCenter) {
                $costCenterId = $costCenter['ID'];
                $workOrders = $this->simproClient->getWorkOrders($companyId, $jobFromSimpro['ID'], $sectionId, $costCenterId);
                foreach ($workOrders as $workOrder) {
                    $allWorkOrders[] = [
                        'job_id' => $jobId,
                        'section_id' => $sectionId,
                        'cost_center_id' => $costCenterId,
                        'work_order_id' => $workOrder['ID'],
                        'name' => (Arr::get($workOrder, 'Staff.Type') === 'employee') ? 'Other Engineer' : Arr::get($workOrder, 'Staff.Name'),
                        'description' => Arr::get($workOrder, 'DescriptionNotes'),
                        'date' => Arr::get($workOrder, 'WorkOrderDate'),
                    ];
                }
            }
        }

        return $allWorkOrders;
    }
}
