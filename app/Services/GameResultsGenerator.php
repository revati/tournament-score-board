<?php namespace App\Services;

use App\Game;
use Faker\Factory;

class GameResultsGenerator
{
    public function generateResults(Game $game)
    {
        return [
            $game->team_a_id => $this->generateScore(),
            $game->team_b_id => $this->generateScore(),
        ];
    }

    private function generateScore()
    {
        return mt_rand(0, 9);
    }
}
