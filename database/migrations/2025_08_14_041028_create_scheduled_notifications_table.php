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
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('channel');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('send_to_all')->default(false);
            $table->json('user_ids')->nullable();
            $table->timestamp('scheduled_at');
            $table->string('status')->default('pending'); // pending, sent, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_notifications');
    }
};
