<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\WebhookTask;
use Revolution\Google\Sheets\Facades\Sheets;

use function PHPUnit\Framework\isEmpty;

class UpdateCardCommand_old extends Command
{
    protected $signature = 'update:trello-card-old';
    protected $description = 'It fetches the data from trello and updates the google sheet.';

    public function handle()
    {
            // Get pending tasks from webhook_tasks table
            $pendingTasks = WebhookTask::where('status', 'pending')->take(5)->get();

            //if no pending task found, return
            if (count($pendingTasks) == 0) {
                $this->info('No pending tasks found.');
                return;
            }

            //accessing sheet
            $spreadsheetId = env('GOOGLE_SPREADSHEET_ID');
            $rows = Sheets::spreadsheet($spreadsheetId)->sheet('sheet1')->all();

            // Extract the header (first row) from the sheet data
            $header = [];
            if (!empty($rows)) {
                $header = array_shift($rows);
            }

            Log::info($header);

            //parameters for api call
            $queryParameters = [
                'key' => env('TRELLO_API_KEY'),
                'token' => env('TRELLO_ACCESS_TOKEN')
            ];

            Log::info($pendingTasks);

            // for each pending card id in the database
            foreach ($pendingTasks as $task) {

                $webhookCardId = $task->webhook_card_id;
                $webhookCardName = $task->webhook_card_name;


                //step1..................create the new record...................
                //...............................................................


                $apiEndpoint = sprintf("%s/cards/%s/checklists", 'https://api.trello.com/1', $webhookCardId);

                $checkLists = Http::get($apiEndpoint, $queryParameters);


                //initializing row for new record in the sheet
                $newRecord = [];
                $atleatOneItemCheck = false;


                if ($checkLists->successful()) {
                    $checkListsData = $checkLists->json();

                    foreach ($header as $headerIndex => $headerItem) {
                        if ($headerIndex == 0) {
                            $newRecord[$headerIndex] = $webhookCardName;
                            continue;
                        }

                        $cleanedHeaderItem = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $headerItem));

                        if($cleanedHeaderItem=="cardid"){
                            $newRecord[$headerIndex] = $webhookCardId;
                            continue;
                        }

                        foreach ($checkListsData as $index => $data) {
                            $name = $data['name'];

                            // Remove non-alphanumeric characters from $name and header row
                            $cleanedName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));

                            if ($cleanedHeaderItem == $cleanedName) {
                                $checkItems = $data['checkItems'];
                                $cellData = "";

                                //get each checkItems array in the card checklist that will be the cell values
                                $nfpa_80_failures = [];
                                foreach ($checkItems as $checkItem) {

                                    if ($checkItem['state'] == "complete") {
                                        $atleatOneItemCheck = true;

                                        if (strtolower($cleanedName) == strtolower("NFPA80FAILURES")) {
                                            $value = $checkItem['name'];
                                            // Use preg_match to extract the number before the dot
                                            preg_match('/\d+/', $value, $matches);

                                            $nfpa_80_failures[] = $matches[0];
                                            $checkItemName = implode(',', $nfpa_80_failures) ;

                                        } else {
                                            $checkItemName = $checkItem['name'];
                                        }

                                        //store all checked names in a string variable separate by ","
                                        if (isEmpty($cellData)) {
                                            $cellData = $checkItemName;
                                        } else {
                                            $cellData = $cellData . "," . $checkItemName;
                                        }
                                    }
                                }
                                //store the newrow
                                $newRecord[$headerIndex] = $cellData;
                            }
                        }
                    }
                }

                Log::info('new record: ', $newRecord);



                if ($atleatOneItemCheck) {
                    $cardUpdated = false;

                    $CardIdColIndex = array_search(strtolower("Card Id"), array_map('strtolower', $header));
                    Log::info('Id index : '. $CardIdColIndex);

                    //check for card record in the sheet, if record is present update it
                    foreach ($rows as $index => $row) {
                        if (isset($row[$CardIdColIndex])) {

                            $SheetCardid = $row[$CardIdColIndex];

                            //if the id is found in the sheet, repopulate the whole card record in the sheet
                            if ($webhookCardId == $SheetCardid) {

                                //return $SheetCardid. " and name is ". $SheetCardName;
                                // Update the row with the modified data
                                $rowIndex = $index + 2; // Rows are 1-based in Google Sheets API
                                $rangeToUpdate = 'Sheet1!A' . $rowIndex . ':Z' . $rowIndex; // Adjust as needed

                                Sheets::spreadsheet($spreadsheetId)->sheet('Sheet1')->range($rangeToUpdate)->update([$newRecord]);

                                // Set $cardExists to true to indicate that the card already exists
                                $cardUpdated = true;
                                break;
                            }
                        }
                    }

                    $cardCreated = false;

                    if ($cardUpdated === false) {

                        //append new record

                        Sheets::spreadsheet($spreadsheetId)->sheet('Sheet1')->append([$newRecord]);
                        $cardCreated = true;
                    }

                    if ($cardCreated || $cardUpdated) {
                        WebhookTask::where('status', 'pending')->where('webhook_card_id', $task->webhook_card_id)->update([
                            'status' => 'completed'
                        ]);
                        $task->update(['status' => 'completed']);
                    }
                }

                $processedCardIds[] = $webhookCardId;
                Log::info('Processed pending task for webhook card IDs: ', $processedCardIds);
            }



            $this->info('Command executed successfully hahahahha.');

    }
}