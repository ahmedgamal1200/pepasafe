<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;


//class DocumentDataImport implements ToCollection
//{
//    public $rows = [];
//
//    public function collection(Collection $rows)
//    {
//        $headers = $rows->first()->toArray(); // أول صف هو رأس الجدول
////        dd($headers);
//        $this->rows = $rows->slice(1)->map(function ($row) use ($headers) {
//            return array_combine($headers, $row->toArray()); // ربط كل صف بالأعمدة
//        })->toArray();
//    }
//}


class DocumentDataImport implements \Maatwebsite\Excel\Concerns\ToCollection
{
    public $rows = [];

    public function collection(\Illuminate\Support\Collection $collection)
    {
        if ($collection->isEmpty()) {
            Log::warning('DocumentDataImport collection is empty.');
            return;
        }

        // Assuming first row is headers, skip it
        $headers = $collection->shift();
        if ($headers === null) {
            Log::warning('DocumentDataImport: No headers found in Excel file.');
            return;
        }
        $headers = $headers->map(fn($item) => strtolower($item))->toArray();
        Log::debug('Excel headers processed.', ['headers' => $headers]);

        foreach ($collection as $rowIndex => $row) {
            if ($row->filter()->isNotEmpty()) { // Only process non-empty rows
                $rowData = $row->toArray();
                if (count($headers) === count($rowData)) {
                    $this->rows[] = array_combine($headers, $rowData);
                    Log::debug("Excel row #{$rowIndex} processed.", ['row_data' => array_combine($headers, $rowData)]);
                } else {
                    Log::warning("Excel row #{$rowIndex} has mismatching number of columns with headers. Skipping.", [
                        'headers_count' => count($headers),
                        'row_data_count' => count($rowData),
                        'row_data' => $rowData
                    ]);
                }
            } else {
                Log::debug("Skipping empty Excel row #{$rowIndex}.");
            }
        }
        Log::debug('DocumentDataImport finished processing collection.', ['final_rows_count' => count($this->rows)]);
    }

}



