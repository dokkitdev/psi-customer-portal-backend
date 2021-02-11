<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupSimproSites\UpdateGroupSimproSiteRequest;
use App\Services\GroupSimproSiteService;
use Symfony\Component\HttpFoundation\Response;

class GroupSimproSiteController extends Controller
{
    public function update(UpdateGroupSimproSiteRequest $request, GroupSimproSiteService $service, $id)
    {
        $service->update($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }
}
