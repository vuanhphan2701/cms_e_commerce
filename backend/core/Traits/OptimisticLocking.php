<?php

namespace Core\Traits;

use Core\Exceptions\OptimisticLockException;

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
                // throw exception in OptimisticLockException if version not provided
                throw new OptimisticLockException("Version not provided in request for optimistic locking.");
            }

            $updated = $model->newQuery()
                ->where($model->getKeyName(), $model->getKey())
                ->where('version', $clientVersion)
                ->update(array_merge(
                    $model->getDirty(),
                    ['version' => $clientVersion + 1]
                ));

            if ($updated === 0) {
                // throw exception in OptimisticLockException if version not provided
                throw new OptimisticLockException("Optimistic lock conflict on " . static::class);
            }

            return false;
        });
    }
}
