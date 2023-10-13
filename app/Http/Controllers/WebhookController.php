<?php

namespace App\Http\Controllers;

use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Http\Request;
use App\Models\WebhookTask;
use App\Models\LastExecutionTimestamp;

class WebhookController extends Controller
{

    public function updateBoard(Request $request)
    {
        app('log')->channel('api')->info($request->all());
        // Get the JSON data from the request
        $response = $request->json()->all();

        if ($response['action']['type']) {

            //get action type
            $ResponseActionType = $response['action']['type'];

            //get card
            $ResponseCardId = $response['action']['data']['card']['id'];
            $ResponseCardName = $response['action']['data']['card']['name'];

            //accessing sheet
            $spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
            $rows = Sheets::spreadsheet($spreadsheetId)->sheet('sheet1')->all();

            // Extract the header (first row) from the sheet data
            $header = [];
            if (!empty($rows)) {
                $header = array_shift($rows);
            }


            //..........................................................................................
            //..........................update check item state on card.................................
            //..........................................................................................

            if ($ResponseActionType == "updateCheckItemStateOnCard") {


                // Store the last execution timestamp in the database
                LastExecutionTimestamp::create(['last_execution' => now()]);

                // Check if a record with the same ResponseCardId and status as 'pending' already exists
                $existingTask = WebhookTask::where('webhook_card_id', $ResponseCardId)
                    ->where('status', 'pending')
                    ->first();

                // If the record doesn't exist, create a new one
                if (!$existingTask) {
                    WebhookTask::create([
                        'webhook_card_id' => $ResponseCardId,
                        'webhook_card_name' => $ResponseCardName,
                        'status' => 'pending',
                    ]);
                }



                // //get the checklist name
                // $checklistName = $response['action']['data']['checklist']['name'];

                // //get the type of state update
                // $actionTypeDetail = $response['action']['display']['translationKey'];

                // //......................................................................................
                // //case1........................if completed state or checked............................
                // //......................................................................................

                // if ($actionTypeDetail == "action_completed_checkitem") {

                //     //get the new checked value
                //     $newValue = $response['action']['display']['entities']['checkitem']['nameHtml'];

                //     //return $newValue;

                //     //get the column index

                //     // Remove non-alphanumeric characters from $checklistName and header row
                //     $cleanedName = preg_replace('/[^a-zA-Z0-9]/', '', $checklistName);
                //     $cleanedHeaderRow = array_map(function ($str) {
                //         return preg_replace('/[^a-zA-Z0-9]/', '', $str);
                //     }, $header);
                //     $colIndexNemeric = array_search(strtolower($cleanedName), array_map('strtolower', $cleanedHeaderRow));
                //     $colIndex = chr(65 + $colIndexNemeric);
                //     //return $colIndex;

                //     //check for card record in the sheet

                //     $cardExists = false;
                //     foreach ($rows as $index => $row) {
                //         $sheetColumn = $row[0];
                //         $SheetCardid = trim(explode("//", $sheetColumn)[1]);
                //         $SheetCardName = trim(explode("//", $sheetColumn)[0]);

                //         //...............if there is a record, update its specific cell ....................

                //         if ($ResponseCardId == $SheetCardid) {

                //             //return $SheetCardid. " and name is ". $SheetCardName;

                //             $rowIndex = $index + 2;
                //             $targetCell = $colIndex . ($rowIndex); //i.e A1, B3, C8
                //             $ExistingCellValue = Sheets::spreadsheet($spreadsheetId)->sheet('Sheet1')->range($targetCell)->get();
                //             //cleaning square brackets from existing value returned by google sheets
                //             $ExistingCellValue = str_replace(['[', ']', '"'], '', $ExistingCellValue);


                //             // Check if the cell is empty or contains a value
                //             if (!empty($ExistingCellValue)) {
                //                 // If the cell contains a value, append the new value to the existing value
                //                 $newValue = $ExistingCellValue .  "," . $newValue;
                //             }

                //             //return "existing cell value is ".$ExistingCellValue. " at index " . $targetCell. " new value is ". $newValue;

                //             // Update the target cell with the new value
                //             Sheets::spreadsheet($spreadsheetId)->sheet('Sheet1')->range($targetCell)->update([[$newValue]]);
                //             $cardExists = true;
                //             break;
                //         }
                //     }
                //     //...............if there is not a card record, create a new record .................

                //     if ($cardExists === false) {

                //         $newRecord = [];
                //         //return $header;
                //         //dd($header);
                //         //return $header;

                //         foreach ($header as $index => $headerItem) {
                //             if ($index == 0) {
                //                 $newRecord[$index] = $ResponseCardName . "//" . $ResponseCardId;
                //                 continue;
                //             }
                //             if ($index == $colIndexNemeric) {
                //                 //set the response checket item value to this index
                //                 $newRecord[$index] = $newValue;
                //                 continue;
                //             } else {
                //                 $newRecord[$index] = "";
                //             }
                //         }

                //         //append new record

                //         Sheets::spreadsheet($spreadsheetId)->sheet('Sheet1')->append([$newRecord]);

                //         //return $newRecord;
                //     }
                // }

                // //..........................................................................................................
                // //case2 ............................if uncompleted state or unchecked..........................................
                // //....................................................................................................


                // if ($actionTypeDetail == "action_marked_checkitem_incomplete") {

                //     //get the column index

                //     // Remove non-alphanumeric characters from $checklistName and header row
                //     $cleanedName = preg_replace('/[^a-zA-Z0-9]/', '', $checklistName);
                //     $cleanedHeaderRow = array_map(function ($str) {
                //         return preg_replace('/[^a-zA-Z0-9]/', '', $str);
                //     }, $header);
                //     $colIndexNemeric = array_search(strtolower($cleanedName), array_map('strtolower', $cleanedHeaderRow));
                //     $colIndex = chr(65 + $colIndexNemeric);
                //     //return $colIndex;


                //     //check for card record in the sheet

                //     foreach ($rows as $index => $row) {
                //         $sheetColumn = $row[0];
                //         $SheetCardid = trim(explode("//", $sheetColumn)[1]);
                //         $SheetCardName = trim(explode("//", $sheetColumn)[0]);

                //         //...............if there is a record, update its specific cell ....................

                //         if ($ResponseCardId == $SheetCardid) {
                //             // return $SheetCardid . " and name is " . $SheetCardName;

                //             $rowIndex = $index + 2;
                //             $targetCell = $colIndex . ($rowIndex); //i.e A1, B3, C8
                //             $ExistingCellValueArray = Sheets::spreadsheet($spreadsheetId)->sheet('Sheet1')->range($targetCell)->get();
                //             $ExistingCellValue = str_replace(['[', ']', '"'], '', $ExistingCellValueArray);

                //             //return $ExistingCellValue;
                //             //remove unchecked item value from the existing cell value
                //             $uncheckedValue = $response['action']['display']['entities']['checkitem']['nameHtml'];
                //             $newValue = null;

                //             // Check if the string contains ',', it means there are more than one values
                //             if (strpos($ExistingCellValue, ',') !== false) {
                //                 //echo "String contains a , symbol.";

                //                 $trimmedExistingCellValues = array_map('trim', explode(",", $ExistingCellValue));
                //                 //return $trimmedExistingCellValues;
                //                 foreach ($trimmedExistingCellValues as $index => $value) {
                //                     //ignore the unchecked value
                //                     if (trim($uncheckedValue) !== $value) {
                //                         if ($newValue == null) {
                //                             $newValue = $value;
                //                         } else {
                //                             $newValue = $newValue . "," . $value;
                //                         }
                //                     }
                //                 }
                //                 //return $newValue;
                //             } else {
                //                 //echo "String does not contain a , symbol.";
                //                 $newValue = "";
                //             }

                //             //return "existing cell value is " . $ExistingCellValue . " at index " . $targetCell . " unchecked value is " . $uncheckedValue;

                //             // Update the target cell with the new value
                //             Sheets::spreadsheet($spreadsheetId)->sheet('Sheet1')->range($targetCell)->update([[$newValue]]);
                //             break;
                //         }
                //     }
                // }
            } //end state update clause
        }
    }
}





//....................... request to create webhook on webhook.site...................................
// λ curl -X POST ^ 
// -H "Content-Type: application/json" ^ 
// -d "{\"key\": \"3a485c0c4218c02d868a0dbbd89e68a0\",
//     \"callbackURL\": \"https://webhook.site/ff4a4824-249e-45e9-9475-5f5b3c8da3d5\",
//     \"idModel\":\"64e79e01e1553c261d3b7a6c\",
//     \"description\": \"My board webhook for updating card in the sheet\"}" ^
//     "https://api.trello.com/1/tokens/ATTA3361530c90d0c67aad38b12b462142ea8a83f6fb3e55c91d0f7ba92610d213860CC4158C/webhooks/"

//..........................response.......................................
//{"id":"651cf32d0a3330789485703c","description":"My board webhook for updating card in the sheet","idModel":"64e79e01e1553c261d3b7a6c","callbackURL":"https://webhook.site/ff4a4824-249e-45e9-9475-5f5b3c8da3d5","active":true,"consecutiveFailures":0,"firstConsecutiveFailDate":null}
