<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'document_title' => 'required|string|max:255',
            'document_message' => 'required|string',
            'document_send_at' => 'required|date',
            'document_send_via' => 'required|array',
            'document_send_via.*' => 'in:email,sms,whatsapp',
            'recipient_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
            'template_data_file_path' => 'required|file|mimes:xlsx,xls|max:2048',
            'document_template_file_path' => 'required|array',
            'document_template_file_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'template_sides' => 'required|array',
            'template_sides.*' => 'in:front,back',
            'document_validity' => 'required|in:permanent,temporary',
            'valid_from' => 'nullable|required_if:document_validity,temporary|date',
            'valid_until' => 'nullable|required_if:document_validity,temporary|date|after_or_equal:valid_from',
            'certificate_text_data' => [
                'required',
                'json',
                function ($attribute, $value, $fail) {
                    $decoded = json_decode($value, true);
                    if (! is_array($decoded)) {
                        $fail('The certificate text data must be a valid JSON object.');
                    }
                    foreach ($decoded as $cardId => $cardData) {
                        if (! is_array($cardData) || ! isset($cardData['type']) || ! is_array($cardData['texts'])) {
                            $fail("Invalid text data structure for card ID: {$cardId}");
                        }
                        foreach ($cardData['texts'] as $text) {
                            if (! is_array($text) || ! isset($text['text'], $text['left'], $text['top'], $text['fontFamily'], $text['fontSize'], $text['fill'], $text['angle'])) {
                                $fail("Invalid text data structure in certificate text data for card ID: {$cardId}");
                            }
                            if (isset($text['textBaseline']) && ! in_array($text['textBaseline'], ['top', 'middle', 'bottom', 'hanging', 'alphabetic', 'alphabetical'])) {
                                $fail("Invalid textBaseline value in certificate text data for card ID: {$cardId}");
                            }
                        }
                    }
                },
            ],

            // Attendance Part
            'is_attendance_enabled' => 'nullable|in:0,1,true,false,on',
            'attendance_send_at' => 'nullable|required_if:is_attendance_enabled,true|date',
            'attendance_message' => 'nullable|required_if:is_attendance_enabled,true|string',
            'attendance_template_data_file_path' => 'nullable|required_if:is_attendance_enabled,true|file|mimes:xlsx,xls|max:2048',
            'attendance_template_file_path' => 'nullable|required_if:is_attendance_enabled,true|array',
            'attendance_template_file_path.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'attendance_template_sides' => 'nullable|required_if:is_attendance_enabled,true|array',
            'attendance_template_sides.*' => 'nullable|in:front,back',
            'attendance_validity' => 'nullable|required_if:is_attendance_enabled,true|in:permanent,temporary',
            'attendance_valid_from' => 'nullable|required_if:attendance_validity,temporary|date',
            'attendance_valid_until' => 'nullable|required_if:attendance_validity,temporary|date|after_or_equal:attendance_valid_from',
            'attendance_text_data' => [
                'nullable',
                'required_if:is_attendance_enabled,true',
                'json',
                function ($attribute, $value, $fail) {
                    if (! $value) {
                        return;
                    }
                    $decoded = json_decode($value, true);
                    if (! is_array($decoded)) {
                        $fail('The attendance text data must be a valid JSON object.');
                    }
                    foreach ($decoded as $cardId => $cardData) {
                        if (! is_array($cardData) || ! isset($cardData['texts']) || ! is_array($cardData['texts'])) {
                            $fail("Invalid text data structure for card ID: {$cardId}");
                        }
                        foreach ($cardData['texts'] as $text) {
                            if (! is_array($text) || ! isset($text['text'], $text['left'], $text['top'], $text['fontFamily'], $text['fontSize'], $text['fill'], $text['angle'])) {
                                $fail("Invalid text data structure in attendance text data for card ID: {$cardId}");
                            }
                            if (isset($text['textBaseline']) && ! in_array($text['textBaseline'], ['top', 'middle', 'bottom', 'hanging', 'alphabetic', 'alphabetical'])) {
                                $fail("Invalid textBaseline value in attendance text data for card ID: {$cardId}");
                            }
                        }
                    }
                },
            ],
        ];
    }
}
