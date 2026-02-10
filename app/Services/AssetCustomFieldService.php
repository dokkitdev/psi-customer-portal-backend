<?php

namespace App\Services;

use App\Repositories\AssetCustomFieldRepository;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetCustomFieldRepository $repository
 * @mixin AssetCustomFieldRepository
 */
class AssetCustomFieldService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(AssetCustomFieldRepository::class);
    }

    public function syncByAsset($asset, $assetId)
    {
        $customFields = $this->repository->get(['asset_id' => $assetId]);

        foreach ($asset['CustomFields'] as $customFieldFromSimpro) {
            $data = [
                'asset_id' => $assetId,
                'custom_field_id' => Arr::get($customFieldFromSimpro, 'CustomField.ID'),
                'name' => Arr::get($customFieldFromSimpro, 'CustomField.Name'),
                'value' => Arr::get($customFieldFromSimpro, 'Value')
            ];
            $customField = $customFields->firstWhere('custom_field_id', Arr::get($customFieldFromSimpro, 'CustomField.ID'));
            if ($customField) {
                $this->repository->update($customField['id'], $data);
                $customFields = $customFields->where('id', '!=', $customField['id']);
            } else {
                $this->repository->create($data);
            }
        }

        if ($customFields->isNotEmpty()) {
            $ids = $customFields->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }
}
