<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteDeclineReasons\CreateQuoteDeclineReasonRequest;
use App\Http\Requests\QuoteDeclineReasons\DeleteQuoteDeclineReasonRequest;
use App\Http\Requests\QuoteDeclineReasons\SearchQuoteDeclineReasonRequest;
use App\Services\QuoteDeclineReasonService;
use Symfony\Component\HttpFoundation\Response;

class QuoteDeclineReasonController extends Controller
{
    public function create(CreateQuoteDeclineReasonRequest $request, QuoteDeclineReasonService $service)
    {
        $result = $service->create($request->onlyValidated());

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function delete(DeleteQuoteDeclineReasonRequest $request, QuoteDeclineReasonService $service, $id)
    {
        $service->delete($id);

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchQuoteDeclineReasonRequest $request, QuoteDeclineReasonService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
