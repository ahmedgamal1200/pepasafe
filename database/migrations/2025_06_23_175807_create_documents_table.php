<?php

use App\Models\DocumentTemplate;
use App\Models\Event;
use App\Models\Recipient;
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
        // ده الداتا اللي اليوزر هيدخلها ب ايده
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('is_attendance_enabled')
                ->default(false); // مفعل الحضور ولا لا
            $table->text('message');
            $table->timestamp('send_at');
            $table->enum('send_via', ['email', 'sms', 'whatsapp'])
                ->default('email');// email , what's up , SMS

            $table->foreignIdFor(Event::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // الناس اللي اتعملهم أكونت ومربوط ليهم شهادة
        Schema::create('recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Event::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('status', ['pending', 'active'])
                ->default('pending');
            $table->timestamps();
        });

        // دي الوثيقة النهائية اللي اليوزر مفروض يتسلمها
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('file_path'); // لينك الشهادة فعليا
            $table->uuid('uuid')
                ->unique()
                ->comment('ده اللي هيتربط ب ال qr_code');
            $table->string('unique_code')
                ->unique()
                ->comment('ده للبحث وانه يظهر ع الشهادة ');
            $table->string('qr_code_path');
            $table->enum('status', ['pending', 'sent'])
                ->default('pending');
            $table->timestamp('sent_at')->nullable();

            $table->foreignIdFor(DocumentTemplate::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Recipient::class)
                ->constrained()
                ->cascadeOnDelete(); // الشخص اللي هيحضر يتنشيء ليه الشهادة دي
            $table->timestamps();
        });

        // الملف الحقيقي اللي اليوزر رفعه كتصميم للوثيقة صورة او بي دي اف
        Schema::create('template_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->enum('file_type', ['pdf', 'image']);
            $table->enum('side', ['front', 'back'])
                ->default('front'); // وجهة واحد ولا وجهين
            $table->foreignIdFor(DocumentTemplate::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // كل فايل أكسيل المستخدم بيرفعه خاص ب الوثيقة
        Schema::create('excel_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->enum('upload_type', ['recipients', 'document_data'])
                ->comment('داتا المستخدمين اللي هيتعمل ليهم اكونتات ولا الداتا اللي هتتحط ع الشهادة ');
            $table->foreignIdFor(Event::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // الاماكن اللي هيتحط فيها النصوص جوه التصميم
        Schema::create('document_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_key')->nullable(); // name
            $table->string('label')->nullable(); // الاسم اللي بيظهر

            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            $table->string('font_family')->nullable();
            $table->integer('font_size')->default(14);
            $table->string('font_color')->default('#000000');
            $table->enum('text_align', ['left', 'center', 'right'])->default('center');
            $table->string('font_weight')->default('normal');
            $table->integer('rotation')->default(0);
            $table->integer('z_index')->default(1); //  ترتيب العنصر في الطبقات

            $table->foreignIdFor(DocumentTemplate::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // ده الجدول اللي بيقول اليوزر ده اتقري ال qr code بتاعه ولا لا
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->timestamp('attended_at')->nullable();
            $table->boolean('qr_scanned')
                ->nullable()
                ->default(false);

            $table->foreignIdFor(Event::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Recipient::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete(); // الشخص اللي هيحضر يتنشيء ليه الشهادة دي
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('document_fields');
        Schema::dropIfExists('excel_uploads');
        Schema::dropIfExists('template_files');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('recipients');
        Schema::dropIfExists('document_templates');

    }
};
