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
        Schema::table('document_templates', function (Blueprint $table) {
            $table->enum('validity', ['temporary', 'permanent'])->default('permanent');
            $table->enum('orientation', ['vertical', 'horizontal'])->default('vertical'); // عمودي = vertical
            $table->enum('paper_type', [
                'A4',
                'A3',
                'A5',
                'A6',
                'Letter',
                'Legal',
                'B5',
                'B4',
                'Executive',
                'Tabloid',
            ])->default('A4');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->date('valid_from')->nullable(); // صلاحية الشخادة
            $table->date('valid_until')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn(['validity', 'paper_orientation', 'paper_type']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['valid_from', 'valid_until']);
        });
    }
};
