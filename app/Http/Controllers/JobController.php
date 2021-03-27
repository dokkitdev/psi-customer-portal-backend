<?php

namespace App\Http\Controllers;

use App\Http\Requests\Jobs\CreateJobRequestRequest;
use App\Http\Requests\Jobs\GetBusinessGroupsRequest;
use App\Http\Requests\Jobs\GetCostCentersRequest;
use App\Http\Requests\Jobs\GetJobRequest;
use App\Http\Requests\Jobs\GetResponseTimesRequest;
use App\Http\Requests\Jobs\SearchJobRequest;
use App\Services\JobService;
use App\Services\SimproService;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    public function createRequest(CreateJobRequestRequest $request, JobService $service)
    {
        $data = Arr::except($request->onlyValidated(), 'files');

        if ($request->has('files')) {
            $files = $request->allFiles();

            foreach ($files['files'] as $file) {
                $data['files'][] = [
                    'content' => file_get_contents($file->getPathname()),
                    'filename' => $file->getClientOriginalName()
                ];
            }
        }

        $result = $service->createRequest($data);

        return response()->json($result, Response::HTTP_CREATED);
    }

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
