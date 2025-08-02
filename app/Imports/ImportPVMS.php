<?php

namespace App\Imports;

use App\Models\PVMS;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportPVMS implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return $row;

    }
}
