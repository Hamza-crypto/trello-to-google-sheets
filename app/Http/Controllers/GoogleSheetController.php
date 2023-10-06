<?php

namespace App\Http\Controllers;

use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Http\Request;

class GoogleSheetController extends Controller
{

    public function index()
    {
        $spreadsheetId = env('GOOGLE_SPREADSHEET_ID');

        // Get all rows from the specified sheet (e.g., Sheet1)
        $rows = Sheets::spreadsheet($spreadsheetId)->sheet('sheet1')->all();

        // Do something with $rows
        //dd($rows);


        // Extract the header (first row) from the rows data
        $header = [];
        if (!empty($rows)) {
            $header = array_shift($rows);
        }

        // Do something with the header
        dd($header);
    }
}
