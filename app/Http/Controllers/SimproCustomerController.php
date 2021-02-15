<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimproCustomers\SearchSimproCustomerRequest;
use App\Services\SimproCustomerService;

class SimproCustomerController extends Controller
{
    public function search(SearchSimproCustomerRequest $request, SimproCustomerService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
