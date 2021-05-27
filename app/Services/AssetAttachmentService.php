<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\AssetAttachmentRepository;
use Illuminate\Support\Facades\Storage;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetAttachmentRepository $repository
 * @mixin AssetAttachmentRepository
 */
class AssetAttachmentService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(AssetAttachmentRepository::class);

        $this->companyId = config('services.simpro.company_id');
        $this->simproClient = app(SimproApiClient::class);
    }

    public function syncByAsset($companyId, $siteIdFromSimpro, $assetIdFromSimpro, $assetId)
    {
        $attachmentsFromSimproPages = $this->simproClient->getAsGenerator(
            "companies/{$companyId}/sites/{$siteIdFromSimpro}/assets/{$assetIdFromSimpro}/attachments/files/"
        );

        $assetAttachments = $this->repository->get(['asset_id' => $assetId]);

        foreach ($attachmentsFromSimproPages as $attachmentsFromSimproPage) {
            foreach ($attachmentsFromSimproPage as $attachmentFromSimpro) {
                $attachmentFromSimproId = $attachmentFromSimpro['ID'];
                $data = [
                    'asset_id' => $assetId,
                    'attachment_id' => $attachmentFromSimproId,
                    'name' => $attachmentFromSimpro['Filename'],
                ];
                $attachment = $assetAttachments->firstWhere('attachment_id', $attachmentFromSimproId);
                if ($attachment) {
                    $this->repository->update($attachment['id'], $data);
                    $assetAttachments = $assetAttachments->where('id', '!=', $attachment['id']);
                } else {
                    $this->repository->create($data);
                }
            }
        }

        if ($assetAttachments->isNotEmpty()) {
            $ids = $assetAttachments->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    public function download($id)
    {
        $attachment = $this->repository
            ->withRelations(['asset.simpro_site'])
            ->find($id);

        $siteId = $attachment['asset']['simpro_site']['site_id'];
        $assetId = $attachment['asset']['asset_id'];
        $attachmentId = $attachment['attachment_id'];

        $file = $this->simproClient->downloadAssetAttachment($this->companyId, $siteId, $assetId, $attachmentId);

        Storage::put($attachmentId, base64_decode($file['Base64Data']));

        return $attachment;
    }
}
