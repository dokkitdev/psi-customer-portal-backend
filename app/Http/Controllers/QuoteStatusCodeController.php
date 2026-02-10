<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteStatusCodes\GetQuoteStatusCodeRequest;
use App\Http\Requests\QuoteStatusCodes\SearchQuoteStatusCodeRequest;
use App\Http\Requests\QuoteStatusCodes\UpdateQuoteStatusCodeRequest;
use App\Services\QuoteStatusCodeService;
use Symfony\Component\HttpFoundation\Response;

class QuoteStatusCodeController extends Controller
{
    public function get(GetQuoteStatusCodeRequest $request, QuoteStatusCodeService $service, $id)
    {
        $result = $service->find($id);

        return response()->json($result);
    }

    public function update(UpdateQuoteStatusCodeRequest $request, QuoteStatusCodeService $service, $id)
    {
        $service->update($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function search(SearchQuoteStatusCodeRequest $request, QuoteStatusCodeService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
