<?php

namespace App\Exports;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function __construct(public Collection $users)
    {
    }

    public function collection(): Collection
    {
        return $this->users->map(function ($user) {
            return [
                'Name' => $user->name,
                'Phone' => $user->phone,
                'Email' => $user->email,
                'Is Attendance' => $user->is_attendance ? 'Yes' : 'No',
                'Attended At' => $user->updated_at ?? 'N/A',

            ];
        });
    }

    public function headings(): array
    {
        return ['Name', 'Phone', 'Email', 'Is Attendance', 'Attended At'];
    }
}
