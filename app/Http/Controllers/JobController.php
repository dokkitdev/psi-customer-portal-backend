<?php

namespace App\Http\Controllers;

use App\Http\Requests\Jobs\GetBusinessGroupsRequest;
use App\Http\Requests\Jobs\GetCostCentersRequest;
use App\Http\Requests\Jobs\GetJobRequest;
use App\Http\Requests\Jobs\GetResponseTimesRequest;
use App\Http\Requests\Jobs\SearchJobRequest;
use App\Services\JobService;
use App\Services\SimproService;

class JobController extends Controller
{
    public function get(GetJobRequest $request, JobService $service, $id)
    {
        $result = $service
            ->withRelations($request->onlyValidated('with', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchJobRequest $request, JobService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }

    public function getResponseTimes(GetResponseTimesRequest $request, SimproService $service)
    {
        $result = $service->getResponseTimes();

        return response()->json($result);
    }

    public function getCostCenters(GetCostCentersRequest $request, SimproService $service)
    {
        $result = $service->getCostCenters();

        return response()->json($result);
    }

    public function getBusinessGroups(GetBusinessGroupsRequest $request, SimproService $service)
    {
        $result = $service->getBusinessGroups();

        return response()->json($result);
    }
}
