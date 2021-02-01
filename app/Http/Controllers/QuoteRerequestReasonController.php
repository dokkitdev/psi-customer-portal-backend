<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRerequestReasons\CreateQuoteRerequestReasonRequest;
use App\Http\Requests\QuoteRerequestReasons\DeleteQuoteRerequestReasonRequest;
use App\Http\Requests\QuoteRerequestReasons\SearchQuoteRerequestReasonRequest;
use App\Services\QuoteRerequestReasonService;
use Symfony\Component\HttpFoundation\Response;

class QuoteRerequestReasonController extends Controller
{
    public function create(CreateQuoteRerequestReasonRequest $request, QuoteRerequestReasonService $service)
    {
        $result = $service->create($request->onlyValidated());

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function delete(DeleteQuoteRerequestReasonRequest $request, QuoteRerequestReasonService $service, $id)
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchQuoteRerequestReasonRequest $request, QuoteRerequestReasonService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
