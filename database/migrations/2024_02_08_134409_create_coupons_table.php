<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('coupon_description')->nullable();
            $table->integer('discount_value')->default(0);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('times_used')->default(1);
            $table->integer('max_usage')->default(1);
            $table->timestamp('coupon_start_date')->nullable();
            $table->timestamp('coupon_end_date')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
