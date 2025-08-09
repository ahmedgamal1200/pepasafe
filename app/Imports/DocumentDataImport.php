<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DocumentDataImport implements ToCollection
{
    public $rows = [];

    public function collection(Collection $rows)
    {
        $headers = $rows->first()->toArray(); // أول صف هو رأس الجدول
//        dd($headers);
        $this->rows = $rows->slice(1)->map(function ($row) use ($headers) {
            return array_combine($headers, $row->toArray()); // ربط كل صف بالأعمدة
        })->toArray();
    }
}
