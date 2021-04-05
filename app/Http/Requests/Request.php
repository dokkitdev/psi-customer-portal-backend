<?php

namespace App\Http\Requests;

use App\Models\Role;
use RonasIT\Support\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Request extends BaseRequest
{
    public function getUserId()
    {
        return $this->user()->id;
    }

    public function isUser()
    {
        return $this->user()->role_id === Role::USER;
    }

    public function validateExistsByPermissions($id, $entityName)
    {
        $service = app("App\Services\\{$entityName}Service");

        if ($this->isUser()) {
            $entity = $service->findByPermissions($id, $this->getUserId());
        } else {
            $entity = $service->find($id);
        }

        if (!$entity) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => $entityName]));
        }

        return $entity;
    }
}