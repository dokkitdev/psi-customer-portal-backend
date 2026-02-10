<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimproCustomers\GetSimproCustomerRequest;
use App\Http\Requests\SimproCustomers\SearchSimproCustomerRequest;
use App\Services\SimproCustomerService;

class SimproCustomerController extends Controller
{
    public function get(GetSimproCustomerRequest $request, SimproCustomerService $service, $id)
    {
        $result = $service
            ->withRelations($request->onlyValidated('with', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchSimproCustomerRequest $request, SimproCustomerService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
