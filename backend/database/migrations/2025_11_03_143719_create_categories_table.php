<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('summary')->nullable();
            $table->foreignId('parent_id')->nullable()
                  ->constrained('categories')->onDelete('set null');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('alias')->nullable();       // present in your ERD
            $table->boolean('status')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_user_id')->nullable()
                  ->constrained('users')->onDelete('set null');
            $table->foreignId('updated_user_id')->nullable()
                  ->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
