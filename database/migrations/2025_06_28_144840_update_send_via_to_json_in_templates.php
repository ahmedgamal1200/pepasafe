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
            $table->dropColumn('send_via');
        });

        Schema::table('document_templates', function (Blueprint $table) {
            $table->json('send_via')->nullable()->after('send_at');
        });

        Schema::table('attendance_templates', function (Blueprint $table) {
            $table->dropColumn('send_via');
        });

        Schema::table('attendance_templates', function (Blueprint $table) {
            $table->json('send_via')->nullable()->after('send_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn('send_via');
        });

        Schema::table('document_templates', function (Blueprint $table) {
            $table->enum('send_via', ['email', 'sms', 'whatsapp'])->default('email');
        });

        Schema::table('attendance_templates', function (Blueprint $table) {
            $table->dropColumn('send_via');
        });

        Schema::table('attendance_templates', function (Blueprint $table) {
            $table->enum('send_via', ['email', 'sms', 'whatsapp'])->default('email');
        });
    }
};
