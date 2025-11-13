<?php

namespace Core\Traits;

use Exception;

/**
 * Trait OptimisticLocking
 *
 * Implements optimistic locking for Eloquent models.
 * Assumes a `version` column exists in the model's table.
 */

trait OptimisticLocking
{

    public static function bootOptimisticLocking()
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
