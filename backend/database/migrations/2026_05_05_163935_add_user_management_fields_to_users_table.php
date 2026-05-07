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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['worker', 'employer', 'both'])->default('worker')->after('email');
            $table->enum('status', ['active', 'banned', 'pending_verification', 'suspended'])->default('active')->after('role');
            $table->string('phone')->nullable()->after('status');
            $table->string('city')->nullable()->after('phone');
            $table->string('province')->nullable()->after('city');
            $table->decimal('rating', 3, 2)->default(0.00)->after('province');
            $table->boolean('is_verified')->default(false)->after('rating');
            $table->string('identity_card_number')->nullable()->after('is_verified');
            $table->string('identity_card_front')->nullable()->after('identity_card_number');
            $table->string('identity_card_back')->nullable()->after('identity_card_front');
            $table->string('zalo_id')->nullable()->after('identity_card_back');
            $table->text('rejection_reason')->nullable()->after('zalo_id');
            $table->timestamp('banned_at')->nullable()->after('rejection_reason');
            $table->timestamp('suspended_until')->nullable()->after('banned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'status', 'phone', 'city', 'province', 'rating', 
                'is_verified', 'identity_card_number', 'identity_card_front', 
                'identity_card_back', 'zalo_id', 'rejection_reason', 
                'banned_at', 'suspended_until'
            ]);
        });
    }
};
