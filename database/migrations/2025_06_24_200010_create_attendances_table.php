<?php

use App\Models\AttendanceTemplate;
use App\Models\DocumentTemplate;
use App\Models\Event;
use App\Models\Recipient;
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
        // 1. Templates for badges
        Schema::create('attendance_templates', function (Blueprint $table) {
            $table->id();
            $table->text('message')->nullable();
            $table->timestamp('send_at')->nullable();
            $table->enum('send_via', ['email', 'sms', 'whatsapp'])->nullable()
                ->default('email');
            $table->foreignIdFor(Event::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // 2. Files for badge template (front/back)
        Schema::create('attendance_template_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_path')->nullable();
            $table->enum('file_type', ['pdf', 'image'])->nullable();
            $table->enum('side', ['front', 'back'])->nullable()
                ->default('front');
            $table->foreignIdFor(AttendanceTemplate::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // الاماكن اللي هيتحط فيها النصوص جوه التصميم
        Schema::create('attendance_document_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_key')->nullable(); // name
            $table->string('label')->nullable(); // الاسم اللي بيظهر

            $table->integer('position_x')
                ->default(0);
            $table->integer('position_y')
                ->default(0);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            $table->string('font_family')->nullable();
            $table->integer('font_size')->nullable()->default(14);
            $table->string('font_color')->nullable()->default('#000000');
            $table->enum('text_align', ['left', 'center', 'right'])->nullable()->default('center');
            $table->string('font_weight')->nullable()->default('normal');
            $table->integer('rotation')->nullable()->default(0);
            $table->integer('z_index')->nullable()->default(1); //  ترتيب العنصر في الطبقات

            $table->foreignIdFor(AttendanceTemplate::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // 4. Final badge document
        Schema::create('attendance_documents', function (Blueprint $table) {
            $table->id();
            $table->string('file_path')->nullable();
            $table->uuid('uuid')
                ->nullable()
                ->unique()
                ->comment('لـ QR');
            $table->string('unique_code')
                ->nullable()
                ->unique()
                ->comment('كود البحث');
            $table->string('qr_code_path')->nullable();
            $table->enum('status', ['pending', 'sent'])
                ->nullable()
                ->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->foreignIdFor(AttendanceTemplate::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Recipient::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // 5. Actual attendance record (QR scanned or not)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamp('attended_at')->nullable();
            $table->boolean('qr_scanned')->default(false);
            $table->foreignIdFor(Event::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Recipient::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('attendance_excel_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('file_path')->nullable();
            $table->foreignIdFor(AttendanceTemplate::class)
                ->nullable()
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
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_documents');
        Schema::dropIfExists('attendance_document_fields');
        Schema::dropIfExists('attendance_template_files');
        Schema::dropIfExists('attendance_templates');
    }
};
