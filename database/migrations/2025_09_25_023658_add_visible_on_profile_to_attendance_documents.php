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
        Schema::table('attendance_documents', function (Blueprint $table) {
            $table->boolean('visible_on_profile')->default(false)->after('status')
                ->comment('Indicates if the attendance document is visible on the user profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_documents', function (Blueprint $table) {
            //
        });
    }
};
