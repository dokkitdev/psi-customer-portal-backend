<?php

namespace App\Models\Traits;

trait SimproPermissionsTrait
{
    public function scopeOnlyPermitted($query, $userId)
    {
        return
            $query
                ->whereHas(self::PERMITTED_CUSTOMERS_RELATION_PATH, function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->whereHas(self::PERMITTED_SITES_RELATION_PATH, function ($query) use ($userId) {
                    $query
                        ->where('is_enabled', true)
                        ->whereHas('group.users', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        });
                });
    }
}