<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrelloController extends Controller
{
    public function getCheckLists($card_id)
    {
        $endpoint = sprintf("/cards/%s/checklists", $card_id);
        return $this->call($endpoint);
    }

    public function getCards($list_id)
    {
        $endpoint = sprintf("/lists/%s/cards", $list_id);
        return $this->call($endpoint);
    }


    public function getLists($board_id)
    {
        $endpoint = sprintf("/boards/%s/lists", $board_id);
        return $this->call($endpoint);
    }


    public function call($endpoint)
    {
        $url = sprintf("%s%s", env('TRELLO_API_URL'), $endpoint);

        $apiKey = env('TRELLO_API_KEY');
        $accessToken = env('TRELLO_API_TOKEN');

        $queryParameters = [
            'key' => $apiKey,
            'token' => $accessToken,
        ];

        $response = Http::get($url, $queryParameters);
        return $response->json();
    }

    function createPositionIndexMap($filePath)
    {
        $positionIndexMap = [];

        $jsonContent = Storage::get($filePath);

        $checklistArray = json_decode($jsonContent, true);

        foreach ($checklistArray as $index => $item) {
            $name = $this->get_checklist_slug($item);
            $positionIndexMap[$name] = $index + 1;

        }

        return $positionIndexMap;
    }

    function get_favorite_columns()
    {
        return [
            "Card Name",
            "ID#",
            "WING",
            "FLOOR",
            "DF#",
            "HANDING",
            "PRIORITY",
            "DOOR RATING",
            "FRAME RATING",
            "DOOR MATERIAL",
            "NFPA80 FAILURES",
            "HOURS TO REPAIR",
            "LOCATION DESCRIPTION",
            "PASS / FAIL / STATUS",
            "HARDWARE NEEDED - COURSE OF ACTION",
            "DOOR REPLACEMENT (NOT INCLUDED IN REPORT)",
            "ADDITIONAL NFPA80 POINTS (NOT INCLUDED IN REPORT)",
            "ADJUSTMENTS",
            "Card ID"
        ];

    }

    //create a function which performs the following operations on string: 1) remove all spaces 2) convert to lowercase 3) make slug
    function get_checklist_slug($string)
    {
        return Str::slug(strtolower(str_replace(' ', '', $string)));
    }
}