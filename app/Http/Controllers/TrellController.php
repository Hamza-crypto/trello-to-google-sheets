<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TrellController extends Controller
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
            $positionIndexMap[$item['pos']] = $index;
        }

        return $positionIndexMap;
    }

    function get_json_file_name()
    {

    }
}