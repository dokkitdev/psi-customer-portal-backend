<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimproSites\SearchSimproSiteRequest;
use App\Services\SimproSiteService;

class SimproSiteController extends Controller
{
    public function search(SearchSimproSiteRequest $request, SimproSiteService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
