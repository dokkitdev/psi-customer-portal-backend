<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\SiteContactRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property SiteContactRepository $repository
 * @mixin SiteContactRepository
 */
class SiteContactService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(SiteContactRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->companyId = config('services.simpro.company_id');
    }

    public function create($data)
    {
        $simproSite = app(SimproSiteService::class)->first($data['simpro_site_id']);

        $contactData = $this->prepareContactData($data);

        $contact = $this->simproClient->postSiteContact($this->companyId, $simproSite['site_id'], $contactData);

        $data['contact_id'] = $contact['ID'];
        $data['name'] = trim("{$data['given_name']} {$data['family_name']}");

        return $this->repository->create($data);
    }

    public function update($where, $data)
    {
        $siteContact = $this->repository->withRelations(['simpro_site'])->first($where);

        $siteId = Arr::get($siteContact, 'simpro_site.site_id');

        $contactData = $this->prepareContactData($data);

        $this->simproClient->patchSiteContact($this->companyId, $siteId, $siteContact['contact_id'], $contactData);

        $givenName = Arr::get($data, 'given_name', $siteContact['given_name']);

        $familyName = Arr::has($data, 'family_name') ? $data['family_name'] : $siteContact['family_name'];

        $data['name'] = trim("{$givenName} {$familyName}");

        return $this->repository->update($where, $data);
    }

    public function delete($where)
    {
        $siteContact = $this->repository->withRelations(['simpro_site'])->first($where);

        $siteId = Arr::get($siteContact, 'simpro_site.site_id');

        $this->simproClient->deleteSiteContact($this->companyId, $siteId, $siteContact['contact_id']);

        return $this->repository->delete($where);
    }

    public function setPrimary($siteContactId)
    {
        return DB::transaction(function () use ($siteContactId) {
            $siteContact = $this->repository->withRelations(['simpro_site'])->find($siteContactId);

            $this->repository->updateMany([
                'simpro_site_id' => Arr::get($siteContact, 'simpro_site.id')
            ], [
                'is_primary' => false
            ]);

            $siteContact = $this->repository->update($siteContactId, [
                'is_primary' => true
            ]);

            $siteId = Arr::get($siteContact, 'simpro_site.site_id');

            $this->simproClient->patchSiteContact($this->companyId, $siteId, $siteContact['contact_id'], [
                'PrimaryContact' => true
            ]);
        });
    }

    public function syncBySite($companyId, $siteIdFromSimpro, $simproSiteId)
    {
        $contacts = $this->simproClient->getSiteContacts($companyId, $siteIdFromSimpro);

        $siteContacts = $this->repository->get(['simpro_site_id' => $simproSiteId]);

        foreach ($contacts as $contact) {
            $data = [
                'simpro_site_id' => $simproSiteId,
                'contact_id' => $contact['ID'],
                'title' => $contact['Title'],
                'name' => trim("{$contact['GivenName']} {$contact['FamilyName']}"),
                'given_name' => $contact['GivenName'],
                'family_name' => $contact['FamilyName'],
                'email' => $contact['Email'],
                'work_phone' => $contact['WorkPhone'],
                'cell_phone' => $contact['CellPhone'],
                'position' => $contact['Position'],
                'is_primary' => $contact['PrimaryContact']
            ];
            $siteContact = $siteContacts->firstWhere('contact_id', $contact['ID']);
            if ($siteContact) {
                $this->repository->update($siteContact['id'], $data);
                $siteContacts = $siteContacts->where('id', '!=', $siteContact['id']);
            } else {
                $this->repository->create($data);
            }
        }

        if ($siteContacts->isNotEmpty()) {
            $ids = $siteContacts->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    protected function prepareContactData($data)
    {
        $siteData = [];

        $siteData['GivenName'] = $data['given_name'];

        if (Arr::has($data, 'family_name')) {
            $siteData['FamilyName'] = $data['family_name'];
        }
        if (Arr::has($data, 'title')) {
            $siteData['Title'] = $data['title'];
        }
        if (Arr::has($data, 'email')) {
            $siteData['Email'] = $data['email'];
        }
        if (Arr::has($data, 'work_phone')) {
            $siteData['WorkPhone'] = $data['work_phone'];
        }
        if (Arr::has($data, 'cell_phone')) {
            $siteData['CellPhone'] = $data['cell_phone'];
        }
        if (Arr::has($data, 'position')) {
            $siteData['Position'] = $data['position'];
        }

        return $siteData;
    }
}
