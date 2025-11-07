<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            // categories fields
            $table->id();
            $table->string('name', 225);
            $table->text('summary')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('alias')->nullable();
            $table->tinyInteger('status')->default(0);

            // Relationship references
            $table->integer('parent_id')->nullable();

            /** This automatically adds:
             * version (int, default 1)
             * created_user_id / updated_user_id (nullable bigint)
             * created_at / updated_at timestamps
             * deleted_at for soft deletes
             */
            $table->commonFields();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
