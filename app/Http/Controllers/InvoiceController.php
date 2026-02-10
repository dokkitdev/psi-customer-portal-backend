<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoices\SearchInvoiceRequest;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function search(SearchInvoiceRequest $request, InvoiceService $service)
    {
        $result = $service->search($request->onlyValidated());

        return response()->json($result);
    }
}
