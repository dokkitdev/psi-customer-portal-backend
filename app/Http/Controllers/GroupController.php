<?php

namespace App\Http\Controllers;

use App\Http\Requests\Groups\CreateGroupRequest;
use App\Http\Requests\Groups\DeleteGroupRequest;
use App\Http\Requests\Groups\ChangeSitesVisibilityRequest;
use App\Http\Requests\Groups\GetGroupRequest;
use App\Http\Requests\Groups\SearchGroupRequest;
use App\Http\Requests\Groups\UpdateGroupRequest;
use App\Services\GroupService;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends Controller
{
    public function create(CreateGroupRequest $request, GroupService $service)
    {
        $result = $service->create($request->onlyValidated());

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function update(UpdateGroupRequest $request, GroupService $service, $id)
    {
        $service->update($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function changeSitesVisibility(ChangeSitesVisibilityRequest $request, GroupService $service, $id)
    {
        $service->changeSitesVisibility($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function delete(DeleteGroupRequest $request, GroupService $service, $id)
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function get(GetGroupRequest $request, GroupService $service, $id)
    {
        $result = $service
            ->withRelations($request->onlyValidated('with'))
            ->find($id);

        return response()->json($result);
    }

    public function search(SearchGroupRequest $request, GroupService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
