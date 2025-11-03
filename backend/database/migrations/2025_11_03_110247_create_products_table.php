<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();

            // Main product fields
            $table->string('sku')->unique();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity')->default(0);

            $table->text('content')->nullable();     // mô tả chi tiết (HTML content)
            $table->text('summary')->nullable();     // mô tả ngắn
            $table->string('image')->nullable();     // ảnh chính
            $table->json('images')->nullable();      // nhiều ảnh dạng JSON

            $table->decimal('average_rating', 2, 1)->default(0); // ví dụ: 4.5
            $table->text('description')->nullable();
            $table->string('alias')->nullable();     // slug
            $table->boolean('status')->default(true);
            $table->integer('version')->default(1);

            // Audit columns
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // Timestamps + soft delete
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['category_id', 'brand_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
