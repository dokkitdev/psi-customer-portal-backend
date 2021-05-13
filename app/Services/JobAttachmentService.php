<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\JobAttachmentRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobAttachmentRepository $repository
 * @mixin JobAttachmentRepository
 */
class JobAttachmentService extends EntityService
{
    protected SimproApiClient $simproClient;

    public function __construct()
    {
        $this->setRepository(JobAttachmentRepository::class);

        $this->simproClient = app(SimproApiClient::class);
    }

    public function syncBySimpro($companyId, $jobIdFromSimpro, $jobId)
    {
        $attachmentsFromSimproPages = $this->simproClient->getAsGenerator(
            "companies/{$companyId}/jobs/{$jobIdFromSimpro}/attachments/files/",
            [
                'columns' => 'ID,Filename,Public,DateAdded',
                'Public' => 'true'
            ]
        );

        $jobAttachments = $this->repository->get(['job_id' => $jobId]);

        foreach ($attachmentsFromSimproPages as $attachmentsFromSimproPage) {
            foreach ($attachmentsFromSimproPage as $attachmentFromSimpro) {
                $attachmentFromSimproId = $attachmentFromSimpro['ID'];
                $data = [
                    'job_id' => $jobId,
                    'attachment_id' => $attachmentFromSimproId,
                    'name' => $attachmentFromSimpro['Filename'],
                    'date_added' => $attachmentFromSimpro['DateAdded'],
                ];
                $attachment = $jobAttachments->firstWhere('attachment_id', $attachmentFromSimproId);
                if ($attachment) {
                    $this->repository->update($attachment['id'], $data);
                    $jobAttachments = $jobAttachments->where('id', '!=', $attachment['id']);
                } else {
                    $this->repository->create($data);
                }
            }
        }

        if ($jobAttachments->isNotEmpty()) {
            $ids = $jobAttachments->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    public function download($id)
    {
        $attachment = $this->repository
            ->withRelations(['job'])
            ->find($id);

        $jobId = $attachment['job']['job_id'];
        $attachmentId = $attachment['attachment_id'];

        $this->simproClient->downloadJobAttachment(0, $jobId, $attachmentId);

        return $attachment;
    }
}
