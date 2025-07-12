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
        Schema::table('wallet_recharge_requests', function (Blueprint $table) {
            $table->dropColumn('reviewed_by');

            // نضيف العمود الجديد كـ foreign key
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // لو الأدمن اتمسح، القيمة تبقى null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_recharge_requests', function (Blueprint $table) {
            // في حالة rollback نحذف الجديد ونرجع القديم
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn('reviewed_by');

            $table->string('reviewed_by')->nullable();
        });
    }
};
