<?php

namespace App\Console\Commands;

use App\Http\Controllers\TrelloController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Webhook;
use Revolution\Google\Sheets\Facades\Sheets;

class UpdateCardCommand extends Command
{
    protected $signature = 'update:trello-card';
    protected $description = 'It fetches the data from trello and updates the google sheet.';

    public function handle()
    {
        $pendingTasks = Webhook::where('status', 'pending')->take(5)->get();

        if (count($pendingTasks) == 0) {
            $this->info('No pending tasks found.');
            return;
        }

        $spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
        $sheet = Sheets::spreadsheet($spreadsheetId)->sheet('sheet1');
        $sheet_rows = $sheet->all();

        // Extract the header (first row) from the sheet data
        $header = [];
        if (!empty($sheet_rows)) {
            $header = array_shift($sheet_rows);
        }

        $special_column_names = [
            'NFPA80 FAILURES',
            'ADDITIONAL NFPA80 POINTS (NOT INCLUDED IN REPORT)'
        ];


        $trello = new TrelloController();

        // These indexes will perform quick searches on the checklist items (when multiple checklist items are selected)
        $nfpa80_mapped_index = $trello->createPositionIndexMap('public/NFPA80_FAILURES.json');
        $additional_nfpa80_mapped_index = $trello->createPositionIndexMap('public/ADDITIONAL_NFPA80.json');

        // for each pending card id in the database
        foreach ($pendingTasks as $task) {

            $cardExists = false;
            $all_data = collect();
            $atleatOneItemCheck = false;

            $webhookCardId = $task->card_id;
            $webhookCardName = $task->card_name;
            dump("Task - $webhookCardName");

            //step1..................create the new record...................
            //...............................................................

            $checkLists = $trello->getCheckLists($webhookCardId);
            $rowData = [
                'Card Name' => trim($webhookCardName),
                'Card ID' => trim($webhookCardId),
            ];

            foreach($checkLists as $checkList) {
                $checklistName = trim($checkList['name']);
                $rowData[$checklistName] = '';

                $mapped_index = [];
                //If this checklist item contains special column name, then we will use the mapped index to get the index number
                if (in_array($checklistName, $special_column_names)) {
                    if($checklistName == 'NFPA80 FAILURES') {
                        $mapped_index = $nfpa80_mapped_index;
                    } elseif($checklistName == 'ADDITIONAL NFPA80 POINTS (NOT INCLUDED IN REPORT)') {
                        $mapped_index = $additional_nfpa80_mapped_index;
                    }

                    $selectedItems = [];

                    foreach ($checkList['checkItems'] as $index => $checkItem) {
                        if ($checkItem['state'] == "complete") {
                            try {
                                $name = $trello->get_checklist_slug($checkItem['name']);
                                $selected_checklist_sequence_number = $mapped_index[$name]; // Put pos value inside mapped_index
                                $selectedItems[] = $selected_checklist_sequence_number;
                            } catch(\Exception $e) {
                                dump($e->getMessage());
                                continue;
                            }
                            $atleatOneItemCheck = true;
                        }
                    }

                    // Join selected item index numbers with commas
                    $rowData[$checklistName] = implode(',', $selectedItems);
                } else {
                    foreach ($checkList['checkItems'] as $checkItem) {
                        if ($checkItem['state'] == "complete") {

                            $rowData[$checklistName] .= $checkItem['name'] . ', ';
                        }
                    }

                    $rowData[$checklistName] = rtrim($rowData[$checklistName], ', ');
                }
            }


            $all_data->push($rowData);
            $dataArray = $all_data->toArray();

            $favoriteColumns = $trello->get_favorite_columns();

            $sortedData = array_map(function ($item) use ($favoriteColumns) {
                $orderedItem = [];

                foreach ($favoriteColumns as $column) {
                    $orderedItem[$column] = $item[$column];
                }

                return $orderedItem;
            }, $dataArray);

            $new_data = $sortedData[0];
            $new_data = array_values($new_data);
            Log::info('new record: ', $new_data);


            if ($atleatOneItemCheck) {
                $cardUpdated = false;
                $cardCreated = false;

                $CardIdColIndex = array_search(strtolower("Card ID"), array_map('strtolower', $header));

                //check for card record in the sheet, if record is present update it
                foreach ($sheet_rows as $index => $row) {
                    if (isset($row[$CardIdColIndex])) {

                        $card_id_from_sheet = $row[$CardIdColIndex];


                        if ($webhookCardId != $card_id_from_sheet) {
                            continue;
                        }
                        $cardCreated = true; // Create new record if card id is not found in the sheet
                        Log::info('Card ID', (array)$card_id_from_sheet);
                        //if the id is found in the sheet, repopulate the whole card record in the sheet

                        //return $SheetCardid. " and name is ". $SheetCardName;
                        // Update the row with the modified data
                        $rowIndex = $index + 2; // Rows are 1-based-indexed in Google Sheets API
                        $rangeToUpdate = 'Sheet1!A' . $rowIndex . ':Z' . $rowIndex; // Adjust as needed
                        dump('Updating row ' . $rowIndex . ' with new data: ', $new_data);

                        Sheets::spreadsheet($spreadsheetId)->sheet('Sheet1')->range($rangeToUpdate)->update([$new_data]);
                        $cardExists = true;
                        // Set $cardExists to true to indicate that the card already exists
                        $cardUpdated = true;
                    }
                }

                if (!$cardExists || count($sheet_rows) == 0) {
                    $sheet->append([$new_data]);
                    dump('Appending new row with data: ', $new_data);
                    $cardCreated = true;
                }

                dump('Loop broken');

                if ($cardCreated || $cardUpdated) {
                    $task->update(['status' => 'completed']);
                }
            }
        }

        $this->info('Command executed successfully hahahahha.');

    }
}