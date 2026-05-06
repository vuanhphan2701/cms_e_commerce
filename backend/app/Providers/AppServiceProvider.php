<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Define a reusable "commonFields" macro for database migrations.
         *
         * You can now call `$table->commonFields()` inside any migration
         * to automatically add:
         * - created_user_id (nullable)
         * - updated_user_id (nullable)
         * - created_at / updated_at timestamps
         * - deleted_at column for soft deletes
         */
        Blueprint::macro('commonFields', function () {
            /** @var \Illuminate\Database\Schema\Blueprint $this */

            // Track which user created and last updated the record
            $this->integer('version')->default(1);

            $this->integer('created_user_id')->nullable();

            $this->integer('updated_user_id')->nullable();

            // Laravel's built-in created_at and updated_at columns
            $this->timestamps();

            // Adds a deleted_at column (used with SoftDeletes trait)
            $this->softDeletes();
        });

    }
}
