<?php

namespace Database\Seeders;

use App\Models\TranslationKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TranslationKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keys = [
            'event.name.label',           // اسم الحدث:
            'event.name.placeholder',     // أدخل اسم الحدث
            'event.issuer.label',         // جهة الاصدار:
            'event.issuer.placeholder',   // أدخل جهة الاصدار
            'event.date.to.label',          // إلى

            'document.title.default',  // مثال: وثيقة 1 أو نموذج 1
            'document.name.label',
            'document.name.placeholder',
            'document.attendance.enable.label', // تفعيل الحضور

            'document.send_date.label',
            'document.message.label',
            'document.message.placeholder',

            'document.send_methods.label',
            'document.send_methods.whatsapp',
            'document.send_methods.email',
            'document.send_methods.sms',

            'document.recipients_excel.label',       // ملف التواصل
            'document.recipients_excel.description', // يحتوي على أسماء المستلمين والبريد الإلكتروني
            'document.choose_recipients_excel.label',

            'document.data_excel.label',       // ملف بيانات الوثيقة
            'document.data_excel.description', // يحتوي على البيانات التي سيتم عرضها داخل الوثيقة
            'document.choose_data_excel.label',

            'document.editor.label',
            'document.paper_type.label',

            'document.orientation.label', // اتجاه التصميم
            'document.orientation.vertical', // طولي
            'document.orientation.horizontal', // عرضي

            // صلاحية الوثيقة
            'document.validity.label',
            'document.validity.temporary',
            'document.validity.permanent',

            // عدد الوجوه
            'document.sides.single',
            'document.sides.double',

            'document.upload.front.label',
            'document.upload.front.note',

            'document.upload.back.label',
            'document.upload.back.note',

            'document.choose_template_front.button',
            'document.choose_template_back.button',

            'document.preview.button',


            //////// الحضور
            'attendance.title.default',

            'attendance.send_date.label',
            'attendance.message.label',
            'attendance.message.placeholder',

            'attendance.send_methods.label',
            'attendance.send_methods.whatsapp',
            'attendance.send_methods.email',
            'attendance.send_methods.sms',

            'attendance.recipients_excel.label',
            'attendance.recipients_excel.description',
            'attendance.choose_recipients_excel.label',

            'attendance.data_excel.label',
            'attendance.data_excel.description',
            'attendance.choose_data_excel.label',

            'attendance.editor.label',
            'attendance.paper_type.label',

            'attendance.orientation.label',
            'attendance.orientation.vertical',
            'attendance.orientation.horizontal',

            'attendance.validity.label',
            'attendance.validity.temporary',
            'attendance.validity.permanent',

            'attendance.sides.single',
            'attendance.sides.double',

            'attendance.upload.front.label',
            'attendance.upload.front.note',

            'attendance.upload.back.label',
            'attendance.upload.back.note',

            'attendance.choose_template_front.button',
            'attendance.choose_template_back.button',

            'attendance.preview.button',

            // ------------------ زر إضافة نموذج/وثيقة جديدة ------------------
            'document.add_new.button',        // إضافة وثيقة جديدة
            'document.add_new.description',   // وصف الزر

            // ------------------ نوتس وتحذيرات ------------------
            'document.notes.label',                     // ملاحظات
            'document.warning.insufficient_balance',    // الرصيد غير كافٍ

            // ------------------ زر إنشاء الحدث ------------------
            'event.create.button',            // إنشاء الحدث

        ];

        foreach ($keys as $key) {
            TranslationKey::query()->firstOrCreate(['key' => $key]);
        }
    }
}
