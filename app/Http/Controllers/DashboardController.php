<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;


class DashboardController extends Controller
{
    public function showDashboard()
    {
        $apiEndpoint = 'https://api.trello.com/1/members/me/boards';

        $queryParameters = [
            'key' => '3a485c0c4218c02d868a0dbbd89e68a0',
            'token' => 'ATTA3361530c90d0c67aad38b12b462142ea8a83f6fb3e55c91d0f7ba92610d213860CC4158C',
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
        $boardId = $request->input('shortLink');
        //initializing sheat 
        $sheat = [];



        //..................................... fetching Lists.........................


        $apiEndpoint = 'https://api.trello.com/1/boards/' . $boardId . '/lists';

        $queryParameters = [
            'key' => '3a485c0c4218c02d868a0dbbd89e68a0', // Replace with your Trello API key
            'token' => 'ATTA3361530c90d0c67aad38b12b462142ea8a83f6fb3e55c91d0f7ba92610d213860CC4158C', // Replace with your Trello access token
        ];

        $lists = Http::get($apiEndpoint, $queryParameters);

        if ($lists->successful()) {
            $listsData = $lists->json();
        } else {
            return response()->json(['error' => 'Failed to fetch data from the API'], $lists->status());
        }

        //.....................................fetching Cards....................................

        $checkListsData = [];
        foreach ($listsData as $list) {

            $apiEndpoint = 'https://api.trello.com/1/lists/' . $list['id'] . '/cards';

            $Cards = Http::get($apiEndpoint, $queryParameters);

            if ($Cards->successful()) {
                $cardsData = $Cards->json();

                //..........................fetching each card check lists....................


                foreach ($cardsData as $card) {
                    $apiEndpoint = 'https://api.trello.com/1/cards/' . $card['id'] . '/checklists';
                    $checkLists = Http::get($apiEndpoint, $queryParameters);

                    if ($checkLists->successful()) {
                        $checkListsData = $checkLists->json();


                        //....................fetching checked data for excel sheat...............



                        //initializing header
                        if (!isset($sheat[0])) {
                            $sheat[0] = []; // Initialize $sheat[0] as an empty array

                            foreach ($checkListsData as $index => $data) {
                                $id = $index;
                                $sheat[0][$id] = $data['name'];
                            }

                            //return $sheat[0];

                            //sorting short strings to long
                            usort($sheat[0], function ($a, $b) {
                                return strlen($a) - strlen($b);
                            });

                            array_unshift($sheat[0], "Card Name");
                        }

                        //initializing row in which check list will be placed
                        $rowData = [];

                        //get each checkList data in the card i.e "name" in each array of card
                        foreach ($checkListsData as $index => $data) {
                            $name = $data['name'];

                            // Remove non-alphanumeric characters from $name and header row
                            $cleanedName = preg_replace('/[^a-zA-Z0-9]/', '', $name);
                            $cleanedHeaderRow = array_map(function ($str) {
                                return preg_replace('/[^a-zA-Z0-9]/', '', $str);
                            }, $sheat[0]);

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
                                        preg_match('/\*\*(\d+)\./', $value, $matches);
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
                                $dardId = $card['id'];
                                $CardNameCellIndex = array_search(strtolower("Card Name"), array_map('strtolower', $sheat[0]));
                                $rowData[$CardNameCellIndex] = $cardName . "//" . $dardId;
                            }
                        }

                        //store rowData in the $sheat
                        $sheat[] = $rowData;
                    } else {
                        return response()->json(['error' => 'Failed to fetch data from the API'], $checkLists->status());
                    }
                }
            } else {
                return response()->json(['error' => 'Failed to fetch data from the API'], $Cards->status());
            }
        }

        //return $sheat;

        //..........................structuring the sheat data........................................

        $structuredSheat = $this->structure($sheat);

        //...........................styling...................................

        $header_style = (new Style())->setFontBold()->setFontSize(10)->setBackgroundColor("0000FF")->setFontColor("FFFFFF");

        $rows_style = (new Style())->setFontSize(12);

        $filePath = storage_path('app/temp/sheat.xlsx');
        $excelFile = new FastExcel($structuredSheat);
        $excelFile->export($filePath);
        //return $structuredSheat;

        //return $excelFile;

        return (new FastExcel($structuredSheat))
            ->headerStyle($header_style)
            ->rowsStyle($rows_style)
            ->download('sheat.xlsx');
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


    private function structure($sheat)
    {
        // Get the maximum number of columns based on the header row
        $maxColumns = count($sheat[0]);

        // Iterate through the existing data to structure the data

        foreach ($sheat as $row) {
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
