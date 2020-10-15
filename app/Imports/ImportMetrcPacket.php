<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\MetrcSourcePacket;
class ImportMetrcPacket implements ToCollection , WithStartRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            MetrcSourcePacket::updateOrCreate(
                [
                    'label' => $row[0]
                ],
                [
                    'source_packet' => $row[2],
                ]);
        }
    }
    public function startRow(): int
    {
        return 2;
    }
}
