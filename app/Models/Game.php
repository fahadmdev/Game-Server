<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'status',
        'player_1_id ',
        'player_2_id ',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function moves()
    {
        return $this->hasMany(Move::class);
    }
}
