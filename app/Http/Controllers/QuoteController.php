<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quotes\ApproveQuoteRequest;
use App\Http\Requests\Quotes\CreateInSimproQuoteRequest;
use App\Http\Requests\Quotes\DeclineQuoteRequest;
use App\Http\Requests\Quotes\DownloadQuoteAttachmentRequest;
use App\Http\Requests\Quotes\ReRequestQuoteRequest;
use App\Http\Requests\Quotes\SearchQuoteRequest;
use App\Services\QuoteService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class QuoteController extends Controller
{
    public function createInSimpro(CreateInSimproQuoteRequest $request, QuoteService $service)
    {
        $data = Arr::except($request->onlyValidated(), 'files');

        if ($request->has('files')) {
            $files = $request->allFiles();

            foreach ($files['files'] as $file) {
                $data['files'][] = [
                    'content' => file_get_contents($file->getPathname()),
                    'filename' => $file->getClientOriginalName()
                ];
            }
        }

        $result = $service->createInSimpro($data);

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function approve(ApproveQuoteRequest $request, QuoteService $service, $id)
    {
        $service->approve($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function decline(DeclineQuoteRequest $request, QuoteService $service, $id)
    {
        $service->decline($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function reRequest(ReRequestQuoteRequest $request, QuoteService $service, $id)
    {
        $service->reRequest($id, $request->onlyValidated());

        return response('', Response::HTTP_NO_CONTENT);
    }

    public function download(DownloadQuoteAttachmentRequest $request, QuoteService $service, $id)
    {
        $quote = $service->download($id);

        return Storage::response($quote['attachment_id'], $quote['attachment_name']);
    }

    public function search(SearchQuoteRequest $request, QuoteService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
