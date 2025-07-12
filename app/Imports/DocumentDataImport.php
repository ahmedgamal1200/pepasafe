<?php

namespace App\Imports;

use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DocumentDataImport implements OnEachRow, WithHeadingRow
{
    public array $rows = [];

    public function onRow(Row $row): void
    {
        $this->rows[] = $row->toArray();
    }
}
