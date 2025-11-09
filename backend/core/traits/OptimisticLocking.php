<?php

namespace Core\Traits;

use Exception;

trait OptimisticLocking
{
    /**
     * Boot the optimistic locking trait.
     * @throws Exception
     */
    public static function bootOptimisticLocking(): void
    {
        static::updating(function ($model) {

            $clientVersion = request()->input('version');

            if (!$clientVersion) {
                throw new Exception("Version not provided in request for optimistic locking.");
            }

            $updated = $model->newQuery()
                ->where($model->getKeyName(), $model->getKey())
                ->where('version', $clientVersion)
                ->update(array_merge(
                    $model->getDirty(),
                    ['version' => $clientVersion + 1]
                ));

            if ($updated === 0) {
                throw new Exception("Optimistic lock conflict on " . static::class);
            }

            return false;
        });
    }
}
