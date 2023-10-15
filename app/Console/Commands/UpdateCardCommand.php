<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\LastExecutionTimestamp;
use App\Models\WebhookTask;
use Illuminate\Support\Carbon;
use Revolution\Google\Sheets\Facades\Sheets;

use function PHPUnit\Framework\isEmpty;

class UpdateCardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-card-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check the last execution timestamp in the database or file
        $lastExecution = LastExecutionTimestamp::latest('id')->first();

        if (
            $lastExecution &&
            Carbon::now()->diffInMinutes($lastExecution->last_execution) >= 1 &&
            Carbon::now()->diffInMinutes($lastExecution->last_execution) < 2
        ) {
            // Execute your logic here


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
                'key' => '3a485c0c4218c02d868a0dbbd89e68a0', // Replace with your Trello API key
                'token' => 'ATTA3361530c90d0c67aad38b12b462142ea8a83f6fb3e55c91d0f7ba92610d213860CC4158C', // Replace with your Trello access token
            ];

            // Get pending tasks from webhook_tasks table
            $pendingTasks = WebhookTask::where('status', 'pending')->get();
            Log::info($pendingTasks);


            // for each pending card id in the database
            foreach ($pendingTasks as $task) {

                $webhookCardId = $task->webhook_card_id;
                $webhookCardName = $task->webhook_card_name;


                //step1..................create the new record...................
                //...............................................................


                $apiEndpoint = 'https://api.trello.com/1/cards/' . $webhookCardId . '/checklists';
                $checkLists = Http::get($apiEndpoint, $queryParameters);


                //initializing row for new record in the sheet
                $newRecord = [];
                $atleatOneItemCheck = false;


                if ($checkLists->successful()) {
                    $checkListsData = $checkLists->json();

                    foreach ($header as $headerIndex => $headerItem) {
                        if ($headerIndex == 0) {
                            $newRecord[$headerIndex] = $webhookCardName . "//" . $webhookCardId;
                            continue;
                        }

                        $cleanedHeaderItem = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $headerItem));

                        foreach ($checkListsData as $index => $data) {
                            $name = $data['name'];

                            // Remove non-alphanumeric characters from $name and header row
                            $cleanedName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));

                            if ($cleanedHeaderItem == $cleanedName) {
                                $checkItems = $data['checkItems'];
                                $cellData = "";

                                //get each checkItems array in the card checklist that will be the cell values

                                foreach ($checkItems as $checkItem) {
                                    if ($checkItem['state'] == "complete") {
                                        $atleatOneItemCheck = true;

                                        if (strtolower($cleanedName) == strtolower("NFPA80FAILURES")) {
                                            $value = $checkItem['name'];
                                            // Use preg_match to extract the number before the dot
                                            preg_match('/\*\*(\d+)\./', $value, $matches);
                                            $checkItemName = $matches[1];
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

                    //check for card record in the sheet, if record is present update it
                    foreach ($rows as $index => $row) {
                        if (isset($row[0])) {
                            $sheetColumn = $row[0];
                            $SheetCardid = trim(explode("//", $sheetColumn)[1]);
                            $SheetCardName = trim(explode("//", $sheetColumn)[0]);


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
                        $task->update(['status' => 'completed']);
                    }
                }

                $processedCardIds[] = $webhookCardId;
            }

            Log::info('Processed pending task for webhook card IDs: ', $processedCardIds);

            $this->info('Command executed successfully hahahahha.');
        } else {
            Log::info('Command skipped.');
        }
    }
}