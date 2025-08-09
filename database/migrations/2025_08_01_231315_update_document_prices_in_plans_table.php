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
            $table->renameColumn('document_price', 'document_price_in_plan');
            $table->integer('document_price_outside_plan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('document_price_in_plan', 'document_price');
            $table->dropColumn('document_price_outside_plan');
        });
    }
};
