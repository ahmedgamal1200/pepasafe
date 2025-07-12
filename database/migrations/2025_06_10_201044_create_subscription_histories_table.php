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
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired']);
            $table->enum('type', ['initial', 'upgrade', 'renewal']);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('pause_date')->nullable();
            $table->date('stop_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
