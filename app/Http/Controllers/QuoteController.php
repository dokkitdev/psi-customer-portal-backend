<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quotes\CreateInSimproQuoteRequest;
use App\Services\QuoteService;
use Illuminate\Support\Arr;
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
}
