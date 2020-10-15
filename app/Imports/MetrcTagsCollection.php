<?php

namespace App\Imports;

use App\MetrcTag;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class MetrcTagsCollection implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            MetrcTag::updateOrCreate(
                [
                'tag' => $row['tag']
                ],
                ['type' => $row['type'],
                    'status' => $row['status'],
                    'commissioned' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['commissioned']),
                ]);
        }
    }
    public function chunkSize(): int
    {
        return 100;
    }

}
