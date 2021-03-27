<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use Illuminate\Support\Arr;

class QuoteService extends BaseService
{
    const TYPE_PPM_QUOTE = 1;
    const TYPE_REMEDIAL_INSTALLATION_QUOTE = 2;

    const TYPES = [
        self::TYPE_PPM_QUOTE,
        self::TYPE_REMEDIAL_INSTALLATION_QUOTE
    ];

    protected SimproApiClient $simproClient;
    protected SettingService $settingService;
    protected $companyId;
    protected SimproSiteService $simproSiteService;

    public function __construct()
    {
        parent::__construct();

        $this->simproClient = app(SimproApiClient::class);
        $this->settingService = app(SettingService::class);
        $this->companyId = config('services.simpro.company_id');
        $this->simproSiteService = app(SimproSiteService::class);
    }

    public function createRequest($data)
    {
        $simproSite = $this->simproSiteService->withRelations(['simpro_customer'])->find($data['simpro_site_id']);

        $defaultTag = $this->settingService->get('default_tag');

        $type = ($data['type'] === self::TYPE_PPM_QUOTE) ? 'Service' : 'Project';

        $quoteData = [
            'Customer' => Arr::get($simproSite, 'simpro_customer.customer_id'),
            'Site' => $simproSite['site_id'],
            'Type' => $type,
            'Tags' => [$defaultTag['ID']],
            'DueDate' => now()->addMonth()->format('Y-m-d')
        ];

        if (Arr::has($data, 'description')) {
            $quoteData['Description'] = $data['description'];
        }

        $quote = $this->simproClient->postQuote($this->companyId, $quoteData);

        if (Arr::has($data, 'files')) {
            foreach ($data['files'] as $file) {
                $this->simproClient->postQuoteAttachment($this->companyId, $quote['ID'], [
                    'Filename' => $file['filename'],
                    'Base64Data' => base64_encode($file['content']),
                    'Public' => true
                ]);
            }
        }

        return $quote;
    }
}
