<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimproSites\GetSimproSiteRequest;
use App\Http\Requests\SimproSites\SearchSimproSiteRequest;
use App\Services\SimproSiteService;

class SimproSiteController extends Controller
{
    public function get(GetSimproSiteRequest $request, SimproSiteService $service, $id)
    {
        $result = $service
            ->withRelations($request->onlyValidated('with', []))
            ->withRelationsCount($request->onlyValidated('with_count', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchSimproSiteRequest $request, SimproSiteService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
