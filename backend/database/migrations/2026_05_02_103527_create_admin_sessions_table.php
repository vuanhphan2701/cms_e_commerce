<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->string('refresh_token')->unique(); // Hashed refresh token
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('admin_id');
            $table->index('refresh_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_sessions');
    }
};
