<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()
                  ->constrained('users')->onDelete('set null');

            $table->tinyInteger('rating')->default(0);      // 1..5
            $table->longText('content')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_verified')->default(false);

            $table->longText('reply_content')->nullable();
            $table->timestamp('reply_at')->nullable();
            $table->foreignId('reply_user_id')->nullable()
                  ->constrained('users')->onDelete('set null');

            $table->unsignedInteger('like_count')->default(0);

            $table->boolean('status')->default(true);
            $table->unsignedInteger('version')->default(1);

            $table->foreignId('created_user_id')->nullable()
                  ->constrained('users')->onDelete('set null');
            $table->foreignId('updated_user_id')->nullable()
                  ->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
