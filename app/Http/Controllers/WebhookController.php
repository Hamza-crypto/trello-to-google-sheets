<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Webhook;

class WebhookController extends Controller
{
    public function updateBoard(Request $request)
    {
        $response = $request->json()->all();

        if ($response['action']['type'] != "updateCheckItemStateOnCard") {
            return;
        }

        //get card
        $data = $response['action']['data'];
        $board_id = $data['board']['id'];
        $card_id = $data['card']['id'];
        $card_name = $data['card']['name'];

        // Check if a record with the same ResponseCardId and status as 'pending' already exists
        $pending_card_exists = Webhook::where('card_id', $card_id)
            ->where('board_id', $board_id)
            ->where('status', 'pending')
            ->exists();

        // If the record doesn't exist, create a new one
        if (!$pending_card_exists) {
            Webhook::create([
                'board_id' => $board_id,
                'card_id' => $card_id,
                'card_name' => $card_name,
                'status' => 'pending',
            ]);
        }
    }
}