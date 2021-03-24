<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\SiteContactRepository;

/**
 * @property SiteContactRepository $repository
 * @mixin SiteContactRepository
 */
class SiteContactService extends BaseService
{
    protected SimproApiClient $simproClient;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(SiteContactRepository::class);

        $this->simproClient = app(SimproApiClient::class);
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
                'name' => "{$contact['GivenName']} {$contact['FamilyName']}",
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
}
