<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->nullable()
                  ->constrained('categories')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()
                  ->constrained('suppliers')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()
                  ->constrained('brands')->onDelete('set null');

            $table->string('sku', 100)->unique();
            $table->string('name', 255);
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity')->default(0);

            $table->longText('content')->nullable();
            $table->text('summary')->nullable();
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->decimal('average_rating', 2, 1)->default(0.0);

            $table->text('description')->nullable();
            $table->string('alias')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('version')->default(1);

            $table->foreignId('created_user_id')->nullable()
                  ->constrained('users')->onDelete('set null');
            $table->foreignId('updated_user_id')->nullable()
                  ->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'brand_id', 'supplier_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
