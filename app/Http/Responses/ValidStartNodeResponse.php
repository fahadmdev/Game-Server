<?php 

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use App\Models\Game;

class ValidStartNodeResponse implements Responsable
{
    protected $game; // Store the game object

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function toResponse($request)
    {
        return response()->json([
            'msg' => 'VALID_START_NODE',
            'body' => [
                'newLine' => null,
                'heading' => 'Player ' . $this->game->player_turn,
                'message' => 'Select a second node to complete the line.',
            ],
        ]);
    }
}