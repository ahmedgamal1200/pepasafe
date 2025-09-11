<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // معلومات الحدث
            'event_title' => ['required', 'string', 'max:255'],
            'issuer' => ['required', 'string', 'max:255'],
            'event_start_date' => ['required', 'date'],
            'event_end_date' => ['required', 'date', 'after_or_equal:event_start_date'],
            'user_id' => ['required', 'exists:users,id'],

            // معلومات الوثيقة
            'document_title' => ['required', 'string', 'max:255'],
            'document_send_at' => ['required', 'date'],
            'document_message' => ['nullable', 'string'],
            'document_send_via' => ['required', 'array', 'min:1'],
            'document_send_via.*' => ['in:email,sms,whatsapp'],
            'orientation' => ['required', 'in:vertical,horizontal'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],

            // معلومات الحضور
            'is_attendance_enabled' => ['nullable', 'boolean'],
            'attendance_send_at' => ['nullable', 'date'],
            'attendance_message' => ['nullable', 'string'],
            'attendance_send_via' => ['required', 'array', 'min:1'],
            'attendance_send_via.*' => ['in:email,sms,whatsapp'],
            'attendance_valid_from' => ['nullable', 'date'],
            'attendance_valid_until' => ['nullable', 'date', 'after_or_equal:attendance_valid_from'],

            // ملفات
            'recipient_file_path' => ['required', 'file', 'mimes:xlsx,csv'],
            'template_data_file_path' => ['nullable', 'file', 'mimes:xlsx,csv'],
            'document_template_file_path' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png'],

            'attendance_template_data_file_path' => ['nullable', 'file', 'mimes:xlsx,csv'],
            'attendance_template_file_path' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png'],

            // أوجه التصميم
            'front' => ['nullable', 'string'],
            'back' => ['nullable', 'string'],
            'face-0' => ['nullable'],
        ];
    }
}
