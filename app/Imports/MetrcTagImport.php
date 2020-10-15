<?php

namespace App\Imports;

use App\MetrcTag;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MetrcTagImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
      //   dd($row);
        return new MetrcTag([
            'tag' => $row['tag'],
            'type' => $row['type'],
            'status' => $row['status'],
            'commissioned' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['commissioned']),
        ]);
    }
}
