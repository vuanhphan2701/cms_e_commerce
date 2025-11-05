<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 225)->unique();
            $table->string('name', 255);
            $table->integer('price');
            $table->integer('quantity')->default(0);
            $table->longText('content')->nullable();
            $table->text('summary')->nullable();
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->decimal('average_rating', 2, 1)->default(0.0);
            $table->text('description')->nullable();
            $table->string('alias')->nullable();
            $table->tinyInteger('status')->default(value: 0);
            /** This automatically adds:
             * version (int, default 1)
             * created_user_id / updated_user_id (nullable bigint)
             * created_at / updated_at timestamps
             * deleted_at for soft deletes
             */
            $table->commonFields();

            $table->index('name');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
