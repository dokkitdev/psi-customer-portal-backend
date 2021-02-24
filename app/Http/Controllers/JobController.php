<?php

namespace App\Http\Controllers;

use App\Http\Requests\Jobs\GetJobRequest;
use App\Http\Requests\Jobs\SearchJobRequest;
use App\Services\JobService;

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
}
