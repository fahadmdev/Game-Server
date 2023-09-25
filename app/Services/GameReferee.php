<?php 

namespace App\Services;

use App\Models\Game;
use App\Models\Move;
use App\Models\Player;

class GameReferee
{
    public function isValidMove(Game $game, Player $currentPlayer, Move $move = null)
    {
        $previousMove = $this->getLastMoveByOppositePlayer($game, $currentPlayer);
    
        if ($previousMove !== null) {
            // Check if the move is not overlapping or intersecting with existing moves
            if ($this->isMoveOverlap($previousMove, $move) || $this->movesIntersect($previousMove, $move)) {
                return false;
            }
    
            // Check if the new move is connected to the previous move (octilinear path)
            if (!$this->isOctilinearPath($previousMove, $move)) {
                return false;
            }
        }
    
        return true;
    }
    

    private function getLastMoveByOppositePlayer(Game $game, Player $currentPlayer)
    {
        $oppositePlayerId = ($currentPlayer->id === $game->player_1_id) ? $game->player_2_id : $game->player_1_id;

        return Move::where('game_id', $game->id)
            ->where('player_id', $oppositePlayerId)
            ->orderBy('id', 'desc')
            ->first();
    }

    private function isOctilinearPath(Move $previousMove, Move $move)
    {
        // Calculate the absolute differences in x and y coordinates
        $xDiff = abs($previousMove->x - $move->x);
        $yDiff = abs($previousMove->y - $move->y);

        // Check if the move forms an octilinear path
        return (
            ($xDiff == 0 && $yDiff == 1) ||  // Vertical path
            ($xDiff == 1 && $yDiff == 0) ||  // Horizontal path
            ($xDiff == 1 && $yDiff == 1)     // 45° diagonal path
        );
    }

    public function isWin(Game $game, Move $move, Player $currentPlayer)
    {
        // Assuming a 4x4 grid with 16 nodes
        $totalNodes = 16;
        $totalMoves = $game->moves->count();

        if ($totalMoves == $totalNodes) {
            return true;
        }

        return false;
    }


    public function isGameCompleted(Game $game)
    {
        // Check if the game status is 'completed'
        return $game->status === 'completed';
    }

    private function isMoveOverlap(Move $previousMove, Move $move)
    {
        // Check if the new move starts or ends at the same position as the previous move
        return ($previousMove->x === $move->x && $previousMove->y === $move->y) ||
            ($previousMove->end_x === $move->x && $previousMove->end_y === $move->y);
    }

    private function movesIntersect(Move $move1, Move $move2)
    {
        // Define the endpoints of the first line segment (move1)
        $x1 = $move1->x;
        $y1 = $move1->y;
        $x2 = $move1->end_x;
        $y2 = $move1->end_y;

        // Define the endpoints of the second line segment (move2)
        $x3 = $move2->x;
        $y3 = $move2->y;
        $x4 = $move2->end_x;
        $y4 = $move2->end_y;

        // Check if the line segments share an endpoint
        if (
            ($x1 === $x3 && $y1 === $y3) || ($x1 === $x4 && $y1 === $y4) ||
            ($x2 === $x3 && $y2 === $y3) || ($x2 === $x4 && $y2 === $y4)
        ) {
            return false; // The line segments share an endpoint and do not intersect.
        }

        // Calculate the direction vectors of the two line segments
        $dx1 = $x2 - $x1;
        $dy1 = $y2 - $y1;
        $dx2 = $x4 - $x3;
        $dy2 = $y4 - $y3;

        // Calculate the determinant of the direction vectors
        $det = $dx1 * $dy2 - $dx2 * $dy1;

        // Check if the line segments are parallel (det == 0)
        if ($det == 0) {
            return false; // The line segments are parallel and do not intersect.
        }

        // Calculate the parameters for the intersection point along each line segment
        $param1 = (($x4 - $x3) * ($y1 - $y3) - ($y4 - $y3) * ($x1 - $x3)) / $det;
        $param2 = (($x2 - $x1) * ($y1 - $y3) - ($y2 - $y1) * ($x1 - $x3)) / $det;

        // Check if the intersection point is within the bounds of both line segments
        if ($param1 >= 0 && $param1 <= 1 && $param2 >= 0 && $param2 <= 1) {
            return true; // The line segments intersect.
        }

        return false; // The line segments do not intersect within their bounds.
    }

}