<?php

namespace App\Repositories\Eventor;

use App\Models\AttendanceTemplate;
use App\Models\ExcelUpload;
use App\Models\DocumentField;

class AttendanceTemplateRepository
{
    public function create(array $data): AttendanceTemplate
    {
        $template = AttendanceTemplate::create([
            'message' => $data['message'],
            'send_at' => $data['send_at'],
            'send_via' => json_encode($data['send_via']),
            'event_id' => $data['event_id'],
            'validity' => $data['validity'],
            'valid_from' => $data['valid_from'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
        ]);

        return $template;
    }

    public function saveTemplateDataFile($file, AttendanceTemplate $template): void
    {
        ExcelUpload::create([
            'file_path' => $file->store('uploads'),
            'upload_type' => 'attendance_data',
            'event_id' => $template->event_id,
        ]);
    }

    public function saveTemplateFiles(AttendanceTemplate $template, array $files, array $sides): void
    {
        foreach ($files as $index => $file) {
            $side = $sides[$index] ?? 'front';
            $fileType = $file->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image';
            $template->templateFiles()->create([
                'file_path' => $file->store('templates', 'public'),
                'file_type' => $fileType,
                'side' => $side,
            ]);
        }
    }

    public function saveFieldPositions(AttendanceTemplate $template, array $textData): void
    {
        if (!is_array($textData)) {
            throw new \Exception('Invalid text data format for attendance fields.');
        }

        // التعامل مع الكائن المتداخل (مثل attendance_template_data_file_path-front)
        foreach ($textData as $cardId => $cardData) {
            if (!isset($cardData['texts']) || !is_array($cardData['texts'])) {
                throw new \Exception("Invalid text data structure for card ID: {$cardId}");
            }

            // استخراج الـ side من cardId (front أو back)
            $side = strpos($cardId, '-front') !== false ? 'front' : 'back';

            foreach ($cardData['texts'] as $text) {
                if (!is_array($text) || !isset($text['text'], $text['left'], $text['top'], $text['fontFamily'], $text['fontSize'], $text['fill'], $text['angle'])) {
                    throw new \Exception("Invalid text obw text data structure for card ID: {$cardId}");
                }

                // إصلاح textBaseline إذا كانت القيمة غير صحيحة
                $textBaseline = isset($text['textBaseline']) && $text['textBaseline'] === 'alphabetical' ? 'alphabetic' : ($text['textBaseline'] ?? 'top');
                if (!in_array($textBaseline, ['top', 'middle', 'bottom', 'hanging', 'alphabetic'])) {
//                    $textBaseline = 'top'; // قيمة افتراضية
                }

                DocumentField::create([
                    'field_key' => $text['text'],
                    'label' => $text['text'],
                    'position_x' => $text['left'],
                    'position_y' => $text['top'],
                    'width' => $text['width'] ?? null,
                    'height' => $text['height'] ?? null,
                    'font_family' => $text['fontFamily'],
                    'font_size' => $text['fontSize'],
                    'font_color' => $text['fill'],
                    'text_align' => $text['textAlign'] ?? 'left',
                    'font_weight' => $text['fontWeight'] ?? 'normal',
                    'rotation' => $text['angle'],
                    'z_index' => $text['zIndex'] ?? 1,
//                    'text_baseline' => $textBaseline,
                    'side' => $side,
                    'attendance_template_id' => $template->id,
                ]);
            }
        }
    }
}
