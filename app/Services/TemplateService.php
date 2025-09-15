<?php

namespace App\Services;

use App\Repositories\Eventor\AttendanceTemplateRepository;
use App\Repositories\Eventor\DocumentTemplateRepository;

class TemplateService
{
    protected $documentTemplateRepository;

    protected $attendanceTemplateRepository;

    public function __construct(
        DocumentTemplateRepository $documentTemplateRepository,
        AttendanceTemplateRepository $attendanceTemplateRepository
    ) {
        $this->documentTemplateRepository = $documentTemplateRepository;
        $this->attendanceTemplateRepository = $attendanceTemplateRepository;
    }

    /**
     * @throws \Exception
     */
    public function createDocumentTemplate(array $data, int $eventId): \App\Models\DocumentTemplate
    {
        $templateData = [
            'title' => $data['document_title'],
            'message' => $data['document_message'],
            'send_at' => $data['document_send_at'],
            'send_via' => $data['document_send_via'],
            'is_attendance_enabled' => $data['is_attendance_enabled'] ?? false,
            'event_id' => $eventId,
            'validity' => $data['document_validity'],
            'valid_from' => $data['document_validity'] === 'temporary' ? $data['valid_from'] : null,
            'valid_until' => $data['document_validity'] === 'temporary' ? $data['valid_until'] : null,
        ];

        $template = $this->documentTemplateRepository->create($templateData);
        $this->documentTemplateRepository->saveTemplateDataFile($data['template_data_file_path'], $eventId);
        $this->documentTemplateRepository->saveTemplateFiles($template, $data['document_template_file_path'], $data['template_sides']);

        $certificateTextData = json_decode($data['certificate_text_data'], true);
        if (! is_array($certificateTextData)) {
            throw new \Exception('Invalid certificate text data format.');
        }
        $this->documentTemplateRepository->saveFieldPositions($template, $certificateTextData);

        return $template;
    }

    /**
     * @throws \Exception
     */
    public function createAttendanceTemplate(array $data, int $eventId): ?\App\Models\AttendanceTemplate
    {
        if (! isset($data['is_attendance_enabled']) || ! $data['is_attendance_enabled']) {
            return null;
        }

        $templateData = [
            'message' => $data['attendance_message'],
            'send_at' => $data['attendance_send_at'],
            'send_via' => $data['document_send_via'],
            'event_id' => $eventId,
            'validity' => $data['attendance_validity'],
            'valid_from' => $data['attendance_validity'] === 'temporary' ? $data['attendance_valid_from'] : null,
            'valid_until' => $data['attendance_validity'] === 'temporary' ? $data['attendance_valid_until'] : null,
        ];

        $template = $this->attendanceTemplateRepository->create($templateData);
        $this->attendanceTemplateRepository->saveTemplateDataFile($data['attendance_template_data_file_path'], $template);
        $this->attendanceTemplateRepository->saveTemplateFiles($template, $data['attendance_template_file_path'], $data['attendance_template_sides']);

        $attendanceTextData = json_decode($data['attendance_text_data'], true);
        if (! is_array($attendanceTextData)) {
            throw new \Exception('Invalid attendance text data format.');
        }
        $this->attendanceTemplateRepository->saveFieldPositions($template, $attendanceTextData);

        return $template;
    }
}
