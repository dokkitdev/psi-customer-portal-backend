<?php

namespace App\Console\Commands;

use App\ApiClients\SimproApiClient;
use App\Services\SiteContactService;

class Tmp extends TimeoutCommand
{
    protected $signature = 'tmp';

    public function handle()
    {
        $companyId = config('services.simpro.company_id');
        $siteIdFromSimpro = 9773;
        $simproSiteId = 7627;

        $simproClient = app(SimproApiClient::class);
        $siteContactService = app(SiteContactService::class);

        $contacts = $simproClient->getSiteContacts($companyId, $siteIdFromSimpro);
        $siteContacts = $siteContactService->get(['simpro_site_id' => $simproSiteId]);
        
        dump($contacts);
        return;

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
                'is_primary' => ($contact['PrimaryContact'] === true)
            ];
            $siteContact = $siteContacts->firstWhere('contact_id', $contact['ID']);
            if ($siteContact) {
                dump('update');
//                $siteContactService->update($siteContact['id'], $data);
//                $siteContacts = $siteContacts->where('id', '!=', $siteContact['id']);
            } else {
                dump("create");
//                $this->repository->create($data);
            }
        }
    }
}

