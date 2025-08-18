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
        Schema::table('plans', function (Blueprint $table) {
            // 💬 القنوات
            // SMS
            $table->integer('sms_price_in_plan')->default(0);
            $table->integer('sms_price_outside_plan')->default(0);

            // WhatsApp
            $table->integer('whatsapp_price_in_plan')->default(0);
            $table->integer('whatsapp_price_outside_plan')->default(0);

            // Email
            $table->integer('email_price_in_plan')->default(0);
            $table->integer('email_price_outside_plan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
};
