<?php

namespace App\Imports;

use App\Models\UserRecords\FalcoData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class FalcoImport implements ToCollection, ToModel
{
    private $current = 0;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

    }
    public function model(array $row)
    {
        $this->current++;
        if($this->current > 1){
            $user = new FalcoData;
            $user->card_no = $row[0];
            $user->id_number = $row[1];
            $user->name = $row[2];
            $user->department = $row[3];
            $user->department = $row[4];
            $user->department = $row[5];
            $user->save();
        }

    }
}
