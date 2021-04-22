<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assets\GetAssetRequest;
use App\Http\Requests\Assets\SearchAssetRequest;
use App\Services\AssetService;

class AssetController extends Controller
{
    public function get(GetAssetRequest $request, AssetService $service, $id)
    {
        $result = $service
            ->withRelations($request->onlyValidated('with', []))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchAssetRequest $request, AssetService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
