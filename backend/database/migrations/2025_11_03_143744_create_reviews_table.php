<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            // product reviews fields
            $table->id();
            $table->tinyInteger('rating')->default(0);      // 1..5
            $table->longText('content')->nullable();
            $table->text('images')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->longText('reply_content')->nullable();
            $table->timestamp('reply_at')->nullable();
            $table->integer('reply_user_id')->nullable();
            $table->integer('like_count')->default(0);
            $table->tinyInteger('status')->default(0);

            // Relationship references
            $table->integer('product_id')->nullable();
            $table->integer('user_id')->nullable();

            /** This automatically adds:
             * version (int, default 1)
             * created_user_id / updated_user_id (nullable bigint)
             * created_at / updated_at timestamps
             * deleted_at for soft deletes
             */
            $table->commonFields();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
