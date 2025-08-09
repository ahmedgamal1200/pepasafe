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
        Schema::table('document_fields', function (Blueprint $table) {
            $table->string('side')->nullable()->after('z_index'); // إضافة عمود side بعد z_index
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_fields', function (Blueprint $table) {
            $table->dropColumn('side'); // حذف العمود لو عايز ترجّع التغييرات
        });
    }
};
