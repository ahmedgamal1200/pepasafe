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
        Schema::create('wallet_recharge_requests', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2); // قيمة الشحن اللي كتبها المستخدم
            $table->string('receipt_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable(); // ملاحظات المشرف لو فيه رفض مثلاً
            $table->timestamp('approved_at')->nullable();
            $table->string('reviewed_by');

            //            $table->foreignId('admin_id')
            //                ->nullable()
            //                ->constrained('users')
            //                ->nullOnDelete(); // الأدمن اللي وافق أو رفض
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Plan::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_recharge_requests');
    }
};
