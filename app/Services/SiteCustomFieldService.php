<?php

namespace App\Services;

use App\Repositories\SiteCustomFieldRepository;
use Illuminate\Support\Arr;

/**
 * @property SiteCustomFieldRepository $repository
 * @mixin SiteCustomFieldRepository
 */
class SiteCustomFieldService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(SiteCustomFieldRepository::class);
    }

    public function createOrUpdateBySite($site, $simproSiteId)
    {
        $customFieldIds = config('defaults.site_custom_field_ids');

        foreach ($customFieldIds as $customFieldId) {
            $customField = $this->findCustomFieldById(Arr::get($site, 'CustomFields', []), $customFieldId);

            $this->repository->updateOrCreate([
                'simpro_site_id' => $simproSiteId,
                'custom_field_id' => $customFieldId
            ], [
                'value' => Arr::get($customField, 'Value')
            ]);
        }
    }

    protected function findCustomFieldById($customFields, $customFieldId)
    {
        return Arr::first($customFields, function ($customField) use ($customFieldId) {
            return $customField['CustomField']['ID'] === $customFieldId;
        }, []);
    }
}
