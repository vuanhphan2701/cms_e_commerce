<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('summary')->nullable();
            $table->string('image')->nullable();
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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
