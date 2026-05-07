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
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            // Change enum to string to support more action types flexibly
            $table->string('action_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->enum('action_type', ['auth', 'read', 'write', 'delete'])->change();
        });
    }
};
