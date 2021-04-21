<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Quote;
use App\Models\Role;
use App\Repositories\QuoteRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property QuoteRepository $repository
 * @mixin QuoteRepository
 */
class QuoteService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected SettingService $settingService;
    protected $companyId;
    protected SimproSiteService $simproSiteService;
    protected JobService $jobService;
    protected SimproCustomerService $simproCustomerService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(QuoteRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->settingService = app(SettingService::class);
        $this->companyId = config('services.simpro.company_id');
        $this->simproSiteService = app(SimproSiteService::class);
        $this->jobService = app(JobService::class);
        $this->simproCustomerService = app(SimproCustomerService::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByIntQuery('quote_id')
            ->filterBy('job_id')
            ->filterByIntQuery('job.job_id', 'simpro_job_id')
            ->filterBy('simpro_customer_id')
            ->filterBy('simpro_site_id')
            ->filterByList('stage', 'stages')
            ->filterByList('status', 'statuses')
            ->filterByList('cost_center_name', 'cost_center_names')
            ->filterBy('value')
            ->filterFrom('value', false, 'value_from')
            ->filterTo('value', false, 'value_to')
            ->filterBy('date_issued')
            ->filterFrom('date_issued', false, 'date_issued_from')
            ->filterTo('date_issued', false, 'date_issued_to')
            ->filterBy('date_expiry')
            ->filterFrom('date_expiry', false, 'date_expiry_from')
            ->filterTo('date_expiry', false, 'date_expiry_to')
            ->filterByQuery(['description'])
            ->filterByNote()
            ->with()
            ->getSearchResults();
    }

    public function createInSimpro($data)
    {
        $simproSite = $this->simproSiteService->withRelations(['simpro_customer'])->find($data['simpro_site_id']);

        $defaultTag = $this->settingService->get('default_tag');

        $type = ($data['type'] === Quote::TYPE_PPM_QUOTE) ? 'Service' : 'Project';

        $quoteStatus = config('defaults.quote_status');

        $quoteData = [
            'Customer' => Arr::get($simproSite, 'simpro_customer.customer_id'),
            'Site' => $simproSite['site_id'],
            'Type' => $type,
            'Tags' => [$defaultTag['ID']],
            'DueDate' => now()->addMonth()->format('Y-m-d'),
            'Status' => $quoteStatus
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

    public function approve($where)
    {
        $quote = $this->repository->first($where);

        $this->simproClient->patchQuote($this->companyId, $quote['quote_id'], [
            'Stage' => Quote::STAGE_APPROVED
        ]);

        return $this->repository->update($where, [
            'stage' => Quote::STAGE_APPROVED
        ]);
    }

    public function decline($where, $data)
    {
        $data['subject'] = 'Decline';

        return $this->declineQuote($where, $data, Quote::STATUS_DECLINED);
    }

    public function reRequest($where, $data)
    {
        $data['subject'] = 'Re-request';

        return $this->declineQuote($where, $data, Quote::STATUS_PENDING);
    }

    public function updateOrCreateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $quoteId = $this->getQuoteId($webhook);

        $quoteFromSimpro = $this->simproClient->getQuote($companyId, $quoteId);

        $simproCustomer = $this->simproCustomerService->getOrCreateBySimpro($companyId, $quoteFromSimpro['Customer']);

        $simproSite = $this->simproSiteService->getOrCreateBySimpro($companyId, $quoteFromSimpro['Site']['ID'], $simproCustomer['id']);

        $jobId = $this->getJobId($companyId, $quoteFromSimpro);

        $quote = $this->repository->first(['quote_id' => $quoteFromSimpro['ID'], 'simpro_site_id' => $simproSite['id']]);

        list($note, $attachment) = $this->getNoteAndAttachment($companyId, $quoteId, $quote['note_id']);

        $quote = $this->createOrUpdate($quoteFromSimpro, $simproSite['id'], $simproCustomer['id'], $jobId, $note, $attachment);

        return $quote;
    }

    public function download($id)
    {
        $quote = $this->repository->find($id);

        $quoteId = $quote['quote_id'];
        $attachmentId = $quote['attachment_id'];

        $file = $this->simproClient->downloadQuoteAttachment($this->companyId, $quoteId, $attachmentId);

        Storage::put($quote['attachment_id'], base64_decode($file['Base64Data']));

        return $quote;
    }

    public function getNoteAndAttachment($companyId, $quoteId, $noteId)
    {
        $note = null;

        if ($noteId) {
            $note = $this->simproClient->getQuoteNote($companyId, $quoteId, $noteId);
        }

        $attachments = $this->simproClient->getQuoteAttachments($companyId, $quoteId);

        $attachment = $this->findMostRecentFile($attachments, ['quote', 'no']);

        if (!$attachment) {
            $attachment = $this->findMostRecentFile($attachments, ['maintenance', 'quotation']);
        }

        return [$note, $attachment];
    }

    protected function getJobId($companyId, $quoteFromSimpro)
    {
        if (Arr::get($quoteFromSimpro, 'JobNo')) {
            $job = $this->jobService->getOrCreateBySimpro($companyId, $quoteFromSimpro['JobNo']);

            return $job['id'];
        }

        return null;
    }

    protected function declineQuote($where, $data, $status)
    {
        $quote = $this->repository->first($where);

        $defaultEmployee = config('defaults.default_employee');

        $note = $this->simproClient->postQuoteNote($this->companyId, $quote['quote_id'], [
            'Subject' => $data['subject'],
            'Note' => Arr::get($data, 'reason'),
            'FollowUpDate' => null,
            'AssignTo' => $defaultEmployee,
        ]);

        return $this->repository->update($where, [
            'status' => $status,
            'note_id' => $note['ID'],
            'note' => $note['Note'],
        ]);
    }

    protected function createOrUpdate($quoteFromSimpro, $simproSiteId, $simproCustomerId, $jobId, $note, $attachment)
    {
        $dateExpiry = null;
        $customField = $this->findCustomFieldById(Arr::get($quoteFromSimpro, 'CustomFields', []));
        if (Arr::get($customField, 'Value')) {
            $dateExpiry = Carbon::createFromFormat('Y-m-d', $customField['Value'])
                ->addDays($quoteFromSimpro['ValidityDays'])
                ->format('Y-m-d');
        }

        return $this->repository->updateOrCreate([
            'quote_id' => $quoteFromSimpro['ID'],
            'simpro_site_id' => $simproSiteId,
        ], [
            'simpro_customer_id' => $simproCustomerId,
            'job_id' => $jobId,
            'quote_id' => $quoteFromSimpro['ID'],
            'date_issued' => $quoteFromSimpro['DateIssued'],
            'status' => $quoteFromSimpro['CustomerStage'],
            'stage' => $quoteFromSimpro['Stage'],
            'description' => $quoteFromSimpro['Description'],
            'cost_center_name' => Arr::get($quoteFromSimpro, 'Sections.0.CostCenters.0.CostCenter.Name'),
            'value' => Arr::get($quoteFromSimpro, 'Total.ExTax'),
            'date_expiry' => $dateExpiry,
            'note_id' => Arr::get($note, 'ID'),
            'note' => Arr::get($note, 'Note'),
            'attachment_id' => Arr::get($attachment, 'ID'),
            'attachment_name' => Arr::get($attachment, 'Filename'),
        ]);
    }

    public function deleteBySimpro($webhook)
    {
        $quoteId = $this->getQuoteId($webhook);

        return $this->repository->delete([
            'quote_id' => $quoteId,
        ]);
    }

    protected function getQuoteId($webhook)
    {
        preg_match('/(\d+)/', $webhook['data']['description'], $matches);

        return $matches[0];
    }

    protected function findCustomFieldById($customFields)
    {
        $customFieldId = Arr::get($this->settingService->get('quote_date_created'), 'ID');

        return collect($customFields)->firstWhere('CustomField.ID', $customFieldId);
    }

    protected function findMostRecentFile($attachments, $needles)
    {
        return collect($attachments)->sortByDesc('DateAdded')->first(function ($attachment) use ($needles) {
            $lowerFilename = Str::lower($attachment['Filename']);

            return Str::containsAll($lowerFilename, $needles);
        });
    }
}
