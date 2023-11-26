<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $apiEndpoint = 'https://api.trello.com/1/members/me/boards';

        $apiKey = env('TRELLO_API_KEY');
        $accessToken = env('TRELLO_ACCESS_TOKEN');

        //parameters for api call
        $queryParameters = [
            'key' => $apiKey,
            'token' => $accessToken,
        ];

        $response = Http::get($apiEndpoint, $queryParameters);

        if ($response->successful()) {
            $responseData = $response->json();

            //return $responseData;
            // Pass the data to the view
            return view('Admin_dashboard.dashboard', ['responseData' => $responseData]);

        // return response()->json($responseData);
        } else {
            return response()->json(['error' => 'Failed to fetch data from the API'], $response->status());
        }
    }

    public function FetchLists(Request $request)
    {
        $boardId = env('BOARD_ID');

        $sheet = [];
        //..................................... fetching Lists.........................


        $apiEndpoint = sprintf("%s/boards/%s/lists", env('TRELLO_API_URL'), $boardId, '/lists');

        $apiKey = env('TRELLO_API_KEY');
        $accessToken = env('TRELLO_API_TOKEN');

        //parameters for api call
        $queryParameters = [
            'key' => $apiKey,
            'token' => $accessToken,
        ];

        try {
            $lists = Http::get($apiEndpoint, $queryParameters);

            if ($lists->successful()) {
                $listsData = $lists->json();
            } else {
                return response()->json(['error' => 'Failed to fetch data from the API'], $lists->status());
            }
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        //.....................................fetching Cards....................................

        $checkListsData = [];
        $count = 1;
        foreach ($listsData as $list) {

            $apiEndpoint = sprintf("%s/lists/%s/cards", env('TRELLO_API_URL'), $list['id']);

            $Cards = Http::get($apiEndpoint, $queryParameters);

            if ($Cards->successful()) {
                $cardsData = $Cards->json();

                //..........................fetching each card check lists....................


                foreach ($cardsData as $card) {
                    dump( "Card Name: " . $card['name']);

                    $apiEndpoint = sprintf("%s/cards/%s/checklists", env('TRELLO_API_URL'), $card['id']);
                    $checkLists = Http::get($apiEndpoint, $queryParameters);

                    if ($checkLists->successful()) {
                        $checkListsData = $checkLists->json();


                        //....................fetching checked data for excel sheat...............

                        //initializing header
                        if (!isset($sheet[0])) {
                            $sheet[0] = []; // Initialize $sheet[0] as an empty array

                            foreach ($checkListsData as $index => $data) {
                                $id = $index;
                                $sheet[0][$id] = $data['name'];
                            }


                            //sorting short strings to long
                            usort($sheet[0], function ($a, $b) {
                                return strlen($a) - strlen($b);
                            });

                            //add "Card Name" at the start
                            array_unshift($sheet[0], "Card Name");

                            // Add "Card Id" column at the end
                            $sheet[0][] = "Card Id";

                            //return $sheet[0];

                        }

                        //initializing row in which check list will be placed
                        $rowData = [];

                        //get each checkList data in the card i.e "name" in each array of card
                        foreach ($checkListsData as $index => $data) {
                            $name = $data['name'];
                            dump($name);
                            // Remove non-alphanumeric characters from $name and header row
                            $cleanedName = preg_replace('/[^a-zA-Z0-9]/', '', $name);
                            $cleanedHeaderRow = array_map(function ($str) {
                                return preg_replace('/[^a-zA-Z0-9]/', '', $str);
                            }, $sheet[0]);

                            $cellIndex = array_search(strtolower($cleanedName), array_map('strtolower', $cleanedHeaderRow));

                            $checkItems = $data['checkItems'];
                            $cellData = null;

                            //get each checkItems array in the card checklist that will be the cell values
                            $atleatOneItemCheck = false;
                            //$id = null;
                            foreach ($checkItems as $checkItem) {

                                if ($checkItem['state'] == "complete") {
                                    $atleatOneItemCheck = true;
                                    if (strtolower($cleanedName) == strtolower("NFPA80FAILURES")) {
                                        $value = $checkItem['name'];
                                        // Use preg_match to extract the number before the dot
                                        preg_match('/\d+/', $value, $matches);
                                        $checkItemName = $matches[1];
                                    } else {
                                        $checkItemName = $checkItem['name'];
                                    }

                                    //store all checked names in a string variable separate by ","
                                    if ($cellData === null) {
                                        $cellData = $checkItemName;
                                    } else {
                                        $cellData = $cellData . "," . $checkItemName;
                                    }
                                }
                            }
                            //store celldata in the rowData
                            $rowData[$cellIndex] = $cellData;

                            //store the card name
                            if ($atleatOneItemCheck === true) {
                                $cardName = $card['name'];
                                $cardId = $card['id'];
                                $CardNameCellIndex = array_search(strtolower("Card Name"), array_map('strtolower', $sheet[0]));
                                $rowData[$CardNameCellIndex] = $cardName;
                                $CardIdCellIndex = array_search(strtolower("Card Id"), array_map('strtolower', $sheet[0]));
                                $rowData[$CardIdCellIndex] = $cardId;
                            }
                        }


                        //store rowData in the $sheet
                        $sheet[] = $rowData;
                    } else {
                        return response()->json(['error' => 'Failed to fetch data from the API checklist'], $checkLists->status());
                    }
                }
            } else {
                return response()->json(['error' => 'Failed to fetch data from the API cards'], $Cards->status());
            }
        }
        return "";
        //return $sheet;

        //..........................structuring the sheat data........................................

        $structuredSheat = $this->structure($sheet);

        //...........................styling...................................

        $header_style = (new Style())->setFontBold()->setFontSize(10)->setBackgroundColor("0000FF")->setFontColor("FFFFFF");

        $rows_style = (new Style())->setFontSize(12);

        // $filePath = storage_path('app/temp/sheet.xlsx');
        // $excelFile = new FastExcel($structuredSheat);
        // $excelFile->export($filePath);
        //return $structuredSheat;

        //return $excelFile;

        dump('File Downloaded successfully');

        return (new FastExcel($structuredSheat))
            ->headerStyle($header_style)
            ->rowsStyle($rows_style)
            ->download('sheet.xlsx');
    }

    private function isAllNotNull($array)
    {
        foreach ($array as $element) {
            if ($element !== null) {
                return true; // If any element is not null, return true
            }
        }
        return false; // All elements are null, return true
    }


    private function structure($sheet)
    {
        // Get the maximum number of columns based on the header row
        $maxColumns = count($sheet[0]);

        // Iterate through the existing data to structure the data

        foreach ($sheet as $row) {
            // Initialize a new row with empty values for all columns
            $newRow = array_fill(0, $maxColumns, null);

            // Fill the new row with values from the existing row
            foreach ($row as $index => $value) {
                $newRow[$index] = $value;
            }

            if ($this->isAllNotNull($newRow)) {
                // Add the new row to the structured data
                $structuredSheat[] = $newRow;
            }
        }

        //preparing sheat for fast excel

        $header = $structuredSheat[0];
        $preparedSheat = [];
        foreach ($structuredSheat as $rowIndex => $row) {
            if ($rowIndex === 0) {
                continue;
            }
            $newRow = [];
            foreach ($row as $colIndex => $value) {
                $key = $header[$colIndex];
                $newRow[$key] = $value;
            }
            $preparedSheat[] = $newRow;
        }

        return $preparedSheat;
    }
}
