<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // إنشاء جدول جديد باسم 'api_configs'
        Schema::create('api_configs', function (Blueprint $table) {
            $table->id(); // عمود ID أساسي
            $table->string('key')->unique(); // مفتاح الإعداد (مثال: 'whatsapp_api_key')
            $table->text('value')->nullable(); // قيمة الإعداد (يمكن أن تكون فارغة)
            $table->string('type')->nullable(); // نوع الإعداد (مثال: 'string', 'int', 'password') - اختياري للمرونة
            $table->timestamps(); // عمودي created_at و updated_at

            // يمكن إضافة أعمدة إضافية هنا إذا كنت بحاجة لتخزين معلومات وصفية أخرى
            // $table->string('description')->nullable();
        });

        // بما أننا نريد تخزين إعدادات محددة (Whatsapp, SMS, SMTP)، يمكننا إدخالها كـ Seed هنا
        // أو يمكنك القيام بذلك يدوياً بعد تشغيل المايجريشن أو عبر Seeder منفصل
        // هذا الجزء اختياري ويمكن حذفه إذا كنت تفضل إدخال البيانات من لوحة التحكم مباشرة
        Schema::table('api_configs', function (Blueprint $table) {
            // إضافة الإعدادات الافتراضية
            DB::table('api_configs')->insert([
                ['key' => 'whatsapp_api_key', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'whatsapp_api_secret', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'whatsapp_phone_number', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'sms_api_key', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'sms_sender_id', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'smtp_host', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'smtp_port', 'value' => null, 'type' => 'int', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'smtp_username', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'smtp_password', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'smtp_from_address', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'smtp_from_name', 'value' => null, 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('api_configs');
    }
};
