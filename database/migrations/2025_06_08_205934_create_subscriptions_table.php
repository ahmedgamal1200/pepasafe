<?php

use App\Models\Plan;
use App\Models\User;
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Plan::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('balance');
            $table->decimal('remaining');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'pending', 'expired'])
                ->default('pending');
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
