<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MoveValidationRequest;
use App\Models\Game;
use App\Models\Move;
use App\Models\Player;
use App\Services\GameReferee;
use Illuminate\Http\Request;
use App\Http\Responses\ValidStartNodeResponse;
use App\Http\Responses\InvalidStartNodeResponse;
use App\Http\Responses\ValidEndNodeResponse;
use App\Http\Responses\InvalidEndNodeResponse;
use App\Http\Responses\GameOverResponse;

class GameController extends Controller
{
    private $gameReferee;

    public function __construct(GameReferee $gameReferee)
    {
        $this->gameReferee = $gameReferee;
    }

    public function initialize(Request $request)
    {
        $game = new Game();
        [$player1, $player2] = $this->createPlayers();

        $game->player_1_id = $player1->id;
        $game->player_2_id = $player2->id;
        $game->player_turn = 1;

        $game->save();
        $this->storeSessionData($request, $game);

        return new ValidStartNodeResponse($game);
    }

    private function createPlayers()
    {
        $player1 = new Player();
        $player1->token = uniqid();
        $player1->save();

        $player2 = new Player();
        $player2->token = uniqid();
        $player2->save();

        return [$player1, $player2];
    }

    public function nodeClicked(MoveValidationRequest $request)
    {
        [$gameId, $currentPlayer] = $this->getSessionData($request);

        $game = Game::find($gameId);

        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        $lastMove = $this->getLastMoveByOppositePlayer($game, $currentPlayer);

        $move = new Move([
            'game_id' => $gameId,
            'x' => $request->input('x'),
            'y' => $request->input('y'),
            'player_id' => $currentPlayer->id
        ]);

        if (!$this->gameReferee->isValidMove($game, $currentPlayer, $move)) {
            return new InvalidEndNodeResponse($game);
        }

        $currentPlayer->moves()->save($move);

        if ($this->gameReferee->isWin($game, $move, $currentPlayer)) {
            $game->status = 'completed';
            $game->save();
            return new GameOverResponse(
                $lastMove->x,
                $lastMove->y,
                $move->x,
                $move->y,
                $game->player_turn
            );
        }

        $game->player_turn = ($game->player_turn == 1) ? 2 : 1;
        $game->save();

        // Update the current player in the session based on the updated player_turn
        $this->updateCurrentPlayerInSession($request, $game);

        if ($this->gameReferee->isGameCompleted($game)) {
            $this->endGameSession($request);
            return new GameOverResponse(
                $lastMove->x,
                $lastMove->y,
                $move->x,
                $move->y,
                $game->player_turn
            );
        }

        if ($lastMove) {
            return new ValidEndNodeResponse(
                $lastMove->x,
                $lastMove->y,
                $move->x,
                $move->y,
                $game->player_turn
            );
        } else {
            return new ValidStartNodeResponse($game);
        }
    }

    private function getLastMoveByOppositePlayer(Game $game, Player $currentPlayer)
    {
        $oppositePlayerId = ($currentPlayer->id === $game->player_1_id) ? $game->player_2_id : $game->player_1_id;

        return Move::where('game_id', $game->id)
            ->where('player_id', $oppositePlayerId)
            ->orderBy('id', 'desc')
            ->first();
    }

    private function getSessionData(Request $request)
    {
        $gameId = $request->session()->get('game_id');
        $playerId = $request->session()->get('current_player_id');

        $game = Game::find($gameId);

        if (!$game) {
            throw new \RuntimeException('Game not found in session');
        }

        $currentPlayer = Player::where('id', $playerId)->first();

        if (!$currentPlayer) {
            throw new \RuntimeException('Current player not found in session');
        }

        return [$gameId, $currentPlayer];
    }

    private function updateCurrentPlayerInSession(Request $request, Game $game)
    {
        $currentPlayer = ($game->player_turn == 1) ? $game->player_1_id : $game->player_2_id;
        $request->session()->put('current_player_id', $currentPlayer);
    }

    private function storeSessionData(Request $request, Game $game)
    {
        $request->session()->put('game_id', $game->id);
        $request->session()->put('current_player_id', $game->player_1_id);
    }

    private function endGameSession(Request $request)
    {
        $request->session()->forget('game_id');
        $request->session()->forget('current_player_id');
    }
}