<?php

namespace App\Http\Controllers\UserRecords;

use App\Http\Controllers\Controller;
use App\Imports\FalcoImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FalcoData extends Controller
{

    public function import_data(){
        return view('bulk-uploading.falcoupload');
    }

    public function import_excel_post(Request $request){
        // dd($request->all());
       Excel::import(new FalcoImport, $request->file('excel_file'));
    }
}
