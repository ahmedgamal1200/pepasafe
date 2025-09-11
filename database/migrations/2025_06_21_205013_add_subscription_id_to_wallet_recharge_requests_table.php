<?php

use App\Models\Subscription;
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
        if (! Schema::hasColumn('wallet_recharge_requests', 'subscription_id')) {
            Schema::table('wallet_recharge_requests', function (Blueprint $table) {
                $table->foreignIdFor(Subscription::class)
                    ->constrained()
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_recharge_requests', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn('subscription_id');
        });
    }
};
